<?php

namespace App\Http\Controllers;

use App\Enums\AssetCondition;
use App\Enums\AssetObjective;
use App\Enums\AssetOwnershipStatus;
use App\Enums\AssetStatus;
use App\Enums\AssetUtilizationStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
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
        unset($data['photos'], $data['delete_photo_ids']);
        $data['certificate_file'] = $request->file('certificate_file')->store('asset-certificates');
        $data['owner_id'] = $request->user()->id;
        $data['status'] = AssetStatus::Draft;
        $asset = DB::transaction(function () use ($data, $photos) {
            $asset = Asset::create($data);
            foreach ($photos as $index => $photo) {
                $asset->photos()->create(['path' => $photo->store('assets', 'public'), 'sort_order' => $index]);
            }

            return $asset;
        });

        return redirect()->route('matching.assets.edit', $asset)->with('success', 'Aset tersimpan sebagai draft. Periksa data lalu kirim untuk review.');
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);

        return view('asset-matching.assets.form', [...$this->formData(), 'asset' => $asset->load('photos')]);
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorize('update', $asset);
        $data = $this->validated($request, $asset);
        $newPhotos = $data['photos'] ?? [];
        unset($data['photos']);
        $deletePhotoIds = $data['delete_photo_ids'] ?? [];
        unset($data['delete_photo_ids']);
        if ($request->hasFile('certificate_file')) {
            Storage::disk('local')->delete($asset->certificate_file);
            $data['certificate_file'] = $request->file('certificate_file')->store('asset-certificates');
        }
        if ($asset->status === AssetStatus::Published) {
            $data['status'] = AssetStatus::PendingReview;
            $data['submitted_at'] = now();
            $data['published_at'] = null;
        }
        DB::transaction(function () use ($asset, $data, $newPhotos, $deletePhotoIds) {
            $asset->update($data);
            $photosToDelete = $asset->photos()->whereIn('id', $deletePhotoIds)->get();
            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->path);
                $photo->delete();
            }
            $offset = $asset->photos()->count();
            foreach ($newPhotos as $index => $photo) {
                $asset->photos()->create(['path' => $photo->store('assets', 'public'), 'sort_order' => $offset + $index]);
            }
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

    public function certificate(Asset $asset)
    {
        $this->authorize('view', $asset);
        abort_unless(Storage::disk('local')->exists($asset->certificate_file), 404);

        return Storage::disk('local')->download($asset->certificate_file);
    }

    public function certificatePreview(Asset $asset)
    {
        $this->authorize('view', $asset);
        abort_unless(Storage::disk('local')->exists($asset->certificate_file), 404);

        return Storage::disk('local')->response(
            $asset->certificate_file,
            basename($asset->certificate_file),
            ['Content-Disposition' => 'inline; filename="'.basename($asset->certificate_file).'"']
        );
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        $validDeleteCount = $asset ? $asset->photos()->whereIn('id', (array) $request->input('delete_photo_ids', []))->count() : 0;
        $photoMax = max(0, 10 - ($asset?->photos()->count() ?? 0) + $validDeleteCount);
        $data = $request->validate([
            'asset_category_id' => ['required', Rule::exists('asset_categories', 'id')->where('is_active', true)],
            'name' => ['required', 'string', 'max:150'], 'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'], 'full_address' => ['required', 'string', 'max:1000'],
            'area_sqm' => ['required', 'numeric', 'min:0.01'], 'certificate_type' => ['required', 'string', 'max:50'],
            'certificate_number' => ['required', 'string', 'max:100'],
            'certificate_file' => [$asset ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'condition' => ['required', Rule::enum(AssetCondition::class)], 'condition_notes' => ['nullable', 'string', 'max:2000'],
            'ownership_status' => ['required', Rule::enum(AssetOwnershipStatus::class)], 'ownership_notes' => ['nullable', 'string', 'max:2000'],
            'utilization_status' => ['required', Rule::enum(AssetUtilizationStatus::class)], 'utilization_notes' => ['nullable', 'string', 'max:2000'],
            'objective' => ['required', Rule::enum(AssetObjective::class)],
            'photos' => [$asset ? 'nullable' : 'required', 'array', 'min:1', 'max:'.$photoMax],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
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
        return ['categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(), 'conditions' => AssetCondition::options(),
            'ownerships' => AssetOwnershipStatus::options(), 'utilizations' => AssetUtilizationStatus::options(), 'objectives' => AssetObjective::options()];
    }
}
