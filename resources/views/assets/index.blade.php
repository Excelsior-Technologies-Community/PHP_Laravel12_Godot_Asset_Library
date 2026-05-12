<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Godot Asset Library</h2>
                <p class="mt-1 text-sm text-gray-600">Browse approved assets, templates, tools, and scripts for Godot.</p>
            </div>

            @auth
                <a href="{{ route('assets.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Upload Asset
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Join to Upload
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('home') }}" class="mb-8 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <div class="grid gap-4 lg:grid-cols-[1fr_220px_auto]">
                    <div>
                        <label for="search" class="mb-1 block text-sm font-medium text-gray-700">Search</label>
                        <input id="search" name="search" value="{{ request('search') }}" type="search" placeholder="Search by title, description, or Godot version" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="category" class="mb-1 block text-sm font-medium text-gray-700">Category</label>
                        <select id="category" name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800 lg:w-auto">
                            Filter
                        </button>
                        <a href="{{ route('home') }}" class="w-full rounded-md border border-gray-300 px-4 py-2 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 lg:w-auto">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($assets as $asset)
                    <article class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <div class="h-48 bg-gray-100">
                            @if($asset->preview_url)
                                <img src="{{ $asset->preview_url }}" alt="{{ $asset->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-gradient-to-br from-gray-100 to-indigo-50 text-sm font-semibold text-gray-500">
                                    No Preview
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $asset->category ?? 'Asset' }}</span>
                                <span class="text-xs text-gray-500">{{ $asset->downloads }} downloads</span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900">{{ $asset->title }}</h3>
                            <p class="mt-2 min-h-12 text-sm leading-6 text-gray-600">{{ Str::limit($asset->description, 120) }}</p>

                            <div class="mt-5 flex items-center justify-between gap-3">
                                <div class="text-xs text-gray-500">
                                    @if($asset->version)
                                        Godot {{ $asset->version }}
                                    @else
                                        Version not set
                                    @endif
                                </div>

                                <a href="{{ route('assets.show', $asset->slug) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center">
                        <h3 class="text-lg font-semibold text-gray-900">No assets found</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            No approved assets are available yet. Pending uploads appear after admin approval.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
