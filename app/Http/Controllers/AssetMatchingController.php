<?php

namespace App\Http\Controllers;

use App\Enums\AssetListingStatus;
use App\Enums\AssetObjective;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetSlugHistory;
use Illuminate\Http\Request;

class AssetMatchingController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::query()->publiclyListed()->with(['category', 'photos'])
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%'))
            ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->input('category'))))
            ->when($request->filled('province'), fn ($q) => $q->where('province', $request->input('province')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', $request->input('city')))
            ->when($request->filled('objective'), fn ($q) => $q->where('objective', $request->input('objective')))
            ->latest('published_at')->paginate(12)->withQueryString();

        return view('asset-matching.index', [
            'assets' => $assets,
            'categories' => AssetCategory::where('is_active', true)->orderBy('name')->get(),
            'objectives' => AssetObjective::options(),
            'provinces' => Asset::publiclyListed()->distinct()->orderBy('province')->pluck('province'),
            'cities' => Asset::publiclyListed()->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(string $asset)
    {
        $resolved = Asset::query()->where('slug', $asset)->orWhere('public_id', $asset)->first();
        if (! $resolved) {
            $resolved = AssetSlugHistory::where('slug', $asset)->with('asset')->first()?->asset;
        }
        abort_unless($resolved?->status === \App\Enums\AssetStatus::Published && $resolved->listing_status !== AssetListingStatus::Inactive, 404);
        if ($asset !== $resolved->slug) {
            return redirect()->route('matching.show', $resolved, 301);
        }

        return view('asset-matching.show', ['asset' => $resolved->load(['category', 'photos', 'facilities'])]);
    }

    public function dashboard(Request $request)
    {
        return view('asset-matching.dashboard', [
            'assets' => $request->user()->assets()->with(['category', 'photos'])->latest()->get(),
            'interests' => $request->user()->assetInterests()->with(['asset.category', 'asset.photos', 'lead'])->latest()->get(),
            'notifications' => $request->user()->notifications()->latest()->take(10)->get(),
        ]);
    }
}
