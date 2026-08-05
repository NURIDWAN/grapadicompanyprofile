<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetListingStatus;
use App\Enums\AssetObjective;
use App\Enums\AssetStatus;
use App\Enums\AssetUtilizationStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OwnerAssetController extends Controller
{
    public function create()
    {
        return view('asset-matching.assets.form', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $photos = $data['photos'];
        $photoAltTexts = $data['photo_alt_texts'] ?? [];
        $facilityIds = $data['facilities'] ?? [];
        unset($data['photos'], $data['photo_alt_texts'], $data['existing_photo_alt'], $data['facilities'], $data['delete_photo_ids']);
        $data['owner_id'] = $request->user()->id;
        $data['status'] = AssetStatus::Draft;
        $asset = DB::transaction(function () use ($data, $photos, $photoAltTexts, $facilityIds) {
            $asset = Asset::create($data);
            foreach ($photos as $index => $photo) {
                $asset->photos()->create([
                    'path' => $photo->store('assets', 'public'),
                    'alt_text' => ($photoAltTexts[$index] ?? null) ?: $asset->name.' - Foto '.($index + 1),
                    'sort_order' => $index,
                ]);
            }
            $asset->facilities()->sync($facilityIds);

            return $asset;
        });

        return redirect()->route('matching.assets.edit', $asset)->with('success', 'Aset tersimpan sebagai draft. Periksa data lalu kirim untuk review.');
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);

        return view('asset-matching.assets.form', [...$this->formData(), 'asset' => $asset->load(['photos', 'facilities'])]);
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        $data = $this->validated($request, $asset);
        $newPhotos = $data['photos'] ?? [];
        $photoAltTexts = $data['photo_alt_texts'] ?? [];
        $existingPhotoAlt = $data['existing_photo_alt'] ?? [];
        $facilityIds = $data['facilities'] ?? [];
        unset($data['photos'], $data['photo_alt_texts'], $data['existing_photo_alt'], $data['facilities']);
        $deletePhotoIds = $data['delete_photo_ids'] ?? [];
        unset($data['delete_photo_ids']);
        if ($asset->slug_locked_at) {
            $data['slug'] = $asset->slug;
        }
        if ($asset->status === AssetStatus::Published) {
            $data['status'] = AssetStatus::PendingReview;
            $data['submitted_at'] = now();
            $data['published_at'] = null;
        }
        DB::transaction(function () use ($asset, $data, $newPhotos, $photoAltTexts, $existingPhotoAlt, $facilityIds, $deletePhotoIds) {
            $asset->update($data);
            foreach ($existingPhotoAlt as $photoId => $altText) {
                $asset->photos()->whereKey($photoId)->update(['alt_text' => $altText ?: $asset->name]);
            }
            $photosToDelete = $asset->photos()->whereIn('id', $deletePhotoIds)->get();
            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }
            $offset = $asset->photos()->count();
            foreach ($newPhotos as $index => $photo) {
                $asset->photos()->create([
                    'path' => $photo->store('assets', 'public'),
                    'alt_text' => ($photoAltTexts[$index] ?? null) ?: $asset->name.' - Foto '.($offset + $index + 1),
                    'sort_order' => $offset + $index,
                ]);
            }
            $asset->facilities()->sync($facilityIds);
        });

        return back()->with('success', 'Data aset berhasil diperbarui.');
    }

    public function submit(Asset $asset)
    {
        $this->authorize('update', $asset);
        abort_unless(in_array($asset->status, [AssetStatus::Draft, AssetStatus::RevisionRequired], true), 422);
        $asset->update(['status' => AssetStatus::PendingReview, 'submitted_at' => now()]);

        return redirect()->route('matching.dashboard')->with('success', 'Aset dikirim dan sedang menunggu review Grapadi.');
    }

    public function archive(Asset $asset)
    {
        abort_unless($asset->owner_id === request()->user()->id, 403);
        $asset->update(['status' => AssetStatus::Archived, 'published_at' => null]);

        return back()->with('success', 'Aset telah diarsipkan.');
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        $rawPhotos = (array) $request->file('photos', []);
        $uploadedPhotos = array_filter($rawPhotos, fn ($file) => $file && $file->isValid());
        if (empty($uploadedPhotos)) {
            $request->offsetUnset('photos');
        } else {
            $request->merge(['photos' => array_values($uploadedPhotos)]);
        }

        if ($request->filled('slug')) {
            $request->merge(['slug' => \Illuminate\Support\Str::slug($request->input('slug'))]);
        }

        $validDeleteCount = $asset ? $asset->photos()->whereIn('id', (array) $request->input('delete_photo_ids', []))->count() : 0;
        $photoMax = max(0, 10 - ($asset?->photos()->count() ?? 0) + $validDeleteCount);
        $data = $request->validate([
            'asset_category_id' => ['required', Rule::exists('asset_categories', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('assets', 'slug')->ignore($asset?->id)],
            'listing_status' => ['required', Rule::enum(AssetListingStatus::class)],
            'province' => ['required', 'string', 'max:100'], 'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'], 'village' => ['required', 'string', 'max:100'],
            'full_address' => ['required', 'string', 'max:1000'], 'google_maps_url' => ['nullable', 'url', 'max:2000'],
            'area_sqm' => ['required', 'numeric', 'min:0.01'], 'price' => ['nullable', 'numeric', 'min:0'],
            'price_per_sqm' => ['nullable', 'numeric', 'min:0'],
            'certificate_type' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string', 'max:10000'],
            'condition' => ['required', Rule::enum(AssetCondition::class)],
            'utilization_status' => ['required', Rule::enum(AssetUtilizationStatus::class)],
            'objective' => ['required', Rule::enum(AssetObjective::class)],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['integer', Rule::exists('facilities', 'id')->where('is_active', true)],
            'photos' => [$asset ? 'nullable' : 'required', 'array', $asset ? 'nullable' : 'min:1', 'max:'.$photoMax],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'photo_alt_texts' => ['nullable', 'array'], 'photo_alt_texts.*' => ['nullable', 'string', 'max:180'],
            'existing_photo_alt' => ['nullable', 'array'], 'existing_photo_alt.*' => ['nullable', 'string', 'max:180'],
            'delete_photo_ids' => ['nullable', 'array'],
            'delete_photo_ids.*' => ['integer', 'exists:asset_photos,id'],
        ]);
        $remainingPhotos = ($asset?->photos()->count() ?? 0) - $validDeleteCount + count($data['photos'] ?? []);
        if ($remainingPhotos < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages(['photos' => 'Aset harus memiliki minimal satu foto.']);
        }

        return $data;
    }

    private function formData(): array
    {
        return [
            'categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(),
            'facilities' => Facility::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'conditions' => AssetCondition::options(), 'listingStatuses' => AssetListingStatus::options(),
            'utilizations' => AssetUtilizationStatus::options(), 'objectives' => AssetObjective::options(),
        ];
    }
}
