<?php

namespace App\Http\Controllers;

use App\Enums\AssetListingStatus;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetInterest;
use App\Models\Lead;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetInterestController extends Controller
{
    public function store(Request $request, Asset $asset)
    {
        abort_unless($asset->status === AssetStatus::Published, 404);
        abort_if(in_array($asset->listing_status, [AssetListingStatus::Closed, AssetListingStatus::Inactive], true), 422, 'Aset ini tidak lagi menerima minat baru.');
        abort_if($asset->owner_id === $request->user()->id, 422, 'Anda tidak dapat menambahkan minat pada aset sendiri.');
        try {
            DB::transaction(function () use ($request, $asset) {
                $interest = AssetInterest::create(['asset_id' => $asset->id, 'user_id' => $request->user()->id]);
                $lead = Lead::create(['user_id' => $request->user()->id, 'asset_id' => $asset->id, 'name' => $request->user()->name,
                    'whatsapp' => $request->user()->whatsapp, 'company' => $request->user()->company, 'status' => 'new',
                    'source' => 'asset_matching', 'notes' => "Minat pada aset: {$asset->name} ({$asset->public_id})"]);
                $interest->update(['lead_id' => $lead->id]);
            });
        } catch (UniqueConstraintViolationException) {
            return back()->with('success', 'Aset ini sudah ada di daftar minat Anda.');
        }

        return redirect()->route('matching.dashboard')->with('success', 'Minat Anda sudah diteruskan kepada tim Grapadi.');
    }
}
