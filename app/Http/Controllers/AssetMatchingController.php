<?php

namespace App\Http\Controllers;

use App\Enums\AssetObjective;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetMatchingController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::query()->published()->with(['category', 'photos'])
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
            'provinces' => Asset::published()->distinct()->orderBy('province')->pluck('province'),
            'cities' => Asset::published()->distinct()->orderBy('city')->pluck('city'),
        ]);
    }

    public function show(Asset $asset)
    {
        abort_unless($asset->status === \App\Enums\AssetStatus::Published, 404);

        return view('asset-matching.show', ['asset' => $asset->load(['category', 'photos'])]);
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
