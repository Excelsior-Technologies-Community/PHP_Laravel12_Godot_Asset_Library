<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('user')->latest()->get();
        return view('admin.assets.index', compact('assets'));
    }

    public function approve($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->status = 'approved';
        $asset->rejection_reason = null;
        $asset->save();

        return back()->with('success', 'Asset approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $asset = Asset::findOrFail($id);
        $asset->status = 'rejected';
        $asset->rejection_reason = $request->rejection_reason;
        $asset->save();

        return back()->with('success', 'Asset rejected with reason');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);

        Storage::disk('public')->delete([
            $asset->asset_file,
            $asset->preview_image,
        ]);

        $asset->delete();

        return back()->with('success', 'Asset deleted successfully');
    }
}
