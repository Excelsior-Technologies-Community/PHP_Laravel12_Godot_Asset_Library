<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    private array $categories = [
        '2D',
        '3D',
        'Scripts',
        'Shaders',
        'Audio',
        'UI',
        'Templates',
        'Tools',
    ];

    public function index(Request $request)
    {
        $assets = Asset::with('user')
            ->where('status', 'approved')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('version', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('category', $request->string('category'));
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $categories = $this->categories;

        return view('assets.index', compact('assets', 'categories'));
    }

    public function show($slug)
    {
        $asset = Asset::with('user')
            ->where('slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        return view('assets.show', compact('asset'));
    }

    public function create()
    {
        $categories = $this->categories;

        return view('assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', Rule::in($this->categories)],
            'version' => ['nullable', 'string', 'max:50'],
            'asset_file' => ['required', 'file', 'max:51200'],
            'preview_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $filePath = $request->file('asset_file')
                            ->store('assets', 'public');

        $previewPath = $request->file('preview_image')
            ? $request->file('preview_image')->store('asset-previews', 'public')
            : null;

        Asset::create([
            'title' => $request->title,
            'slug' => $this->uniqueSlug($request->title),
            'description' => $request->description,
            'category' => $request->category,
            'version' => $request->version,
            'asset_file' => $filePath,
            'preview_image' => $previewPath,
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect('/')->with('success', 'Asset uploaded for review');
    }

    public function edit(Asset $asset)
    {
        $this->authorizeAssetOwner($asset);

        $categories = $this->categories;

        return view('assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $this->authorizeAssetOwner($asset);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', Rule::in($this->categories)],
            'version' => ['nullable', 'string', 'max:50'],
            'asset_file' => ['nullable', 'file', 'max:51200'],
            'preview_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'version' => $request->version,
            'status' => 'pending',
            'rejection_reason' => null,
        ];

        if ($asset->title !== $request->title) {
            $data['slug'] = $this->uniqueSlug($request->title, $asset->id);
        }

        if ($request->hasFile('asset_file')) {
            Storage::disk('public')->delete($asset->asset_file);
            $data['asset_file'] = $request->file('asset_file')->store('assets', 'public');
        }

        if ($request->hasFile('preview_image')) {
            Storage::disk('public')->delete($asset->preview_image);
            $data['preview_image'] = $request->file('preview_image')->store('asset-previews', 'public');
        }

        $asset->update($data);

        return redirect()->route('dashboard')->with('success', 'Asset updated and sent for review');
    }

    public function download(Asset $asset)
    {
        abort_unless($asset->status === 'approved', 404);

        $asset->increment('downloads');

        return Storage::disk('public')->download($asset->asset_file);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Asset::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function authorizeAssetOwner(Asset $asset): void
    {
        abort_unless($asset->user_id === Auth::id(), 403);
    }
}
