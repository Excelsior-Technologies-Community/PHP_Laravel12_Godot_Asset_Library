<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $asset->title }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ $asset->category ?? 'Asset' }} by {{ $asset->user->name }}</p>
            </div>

            <a href="{{ route('assets.download', $asset) }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                Download Asset
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-[1fr_320px] lg:px-8">
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="h-72 bg-gray-100 sm:h-96">
                    @if($asset->preview_url)
                        <img src="{{ $asset->preview_url }}" alt="{{ $asset->title }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center bg-gradient-to-br from-gray-100 to-indigo-50 text-sm font-semibold text-gray-500">
                            No Preview Image
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                    <p class="mt-3 whitespace-pre-line leading-7 text-gray-700">{{ $asset->description }}</p>
                </div>
            </div>

            <aside class="space-y-4">
                <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">Asset Info</h3>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">Category</dt>
                            <dd class="font-medium text-gray-900">{{ $asset->category ?? 'Not set' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">Godot Version</dt>
                            <dd class="font-medium text-gray-900">{{ $asset->version ?? 'Not set' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">Downloads</dt>
                            <dd class="font-medium text-gray-900">{{ $asset->downloads }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-gray-500">Uploaded</dt>
                            <dd class="font-medium text-gray-900">{{ $asset->created_at->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>

                @auth
                    @if($asset->user_id === Auth::id())
                        <a href="{{ route('assets.edit', $asset) }}" class="block rounded-md border border-gray-300 bg-white px-4 py-2 text-center text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                            Edit Asset
                        </a>
                    @endif
                @endauth
            </aside>
        </div>
    </div>
</x-app-layout>
