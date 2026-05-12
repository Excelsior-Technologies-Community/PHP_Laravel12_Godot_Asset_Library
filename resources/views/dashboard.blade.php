<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">My Assets</h2>
                <p class="mt-1 text-sm text-gray-600">Track your uploaded assets and edit submissions.</p>
            </div>

            <a href="{{ route('assets.create') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                Upload Asset
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Downloads</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($assets as $asset)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-14 w-20 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                                @if($asset->preview_url)
                                                    <img src="{{ $asset->preview_url }}" alt="{{ $asset->title }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full items-center justify-center text-xs font-semibold text-gray-400">No image</div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $asset->title }}</div>
                                                @if($asset->rejection_reason)
                                                    <div class="mt-1 text-sm text-red-600">{{ $asset->rejection_reason }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->category ?? 'Not set' }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                                            @if($asset->status === 'approved') bg-green-100 text-green-800
                                            @elseif($asset->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->downloads }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('assets.edit', $asset) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">Edit</a>
                                        @if($asset->status === 'approved')
                                            <span class="mx-2 text-gray-300">|</span>
                                            <a href="{{ route('assets.show', $asset->slug) }}" class="text-sm font-semibold text-gray-700 hover:text-gray-900">View</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        You have not uploaded any assets yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
