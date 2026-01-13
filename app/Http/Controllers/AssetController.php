<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::where('status', 'approved')->latest()->get();
        return view('assets.index', compact('assets'));
    }

    public function show($slug)
    {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        return view('assets.show', compact('asset'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'asset_file' => 'required|file'
        ]);

        $filePath = $request->file('asset_file')
                            ->store('assets', 'public');

        Asset::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'asset_file' => $filePath,
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect('/')->with('success', 'Asset uploaded for review');
    }
}
