<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;

class AdminAssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->get();
        return view('admin.assets.index', compact('assets'));
    }

    public function approve($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->status = 'approved';
        $asset->save();

        return back();
    }
}
