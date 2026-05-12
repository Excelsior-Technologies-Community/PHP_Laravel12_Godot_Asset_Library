<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Admin Asset Management</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 rounded-md border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">All Uploaded Assets</h3>
                    <span class="text-sm text-gray-500">Total: {{ $assets->count() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Asset</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Uploaded By</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Review</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Delete</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($assets as $asset)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <div class="flex gap-3">
                                            <div class="h-14 w-20 shrink-0 overflow-hidden rounded-md bg-gray-100">
                                                @if($asset->preview_url)
                                                    <img src="{{ $asset->preview_url }}" alt="{{ $asset->title }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full items-center justify-center text-xs font-semibold text-gray-400">No image</div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $asset->title }}</div>
                                                <div class="mt-1 text-xs text-gray-500">{{ $asset->category ?? 'Not set' }} @if($asset->version) - Godot {{ $asset->version }} @endif</div>
                                                @if($asset->status === 'approved')
                                                    <a href="{{ route('assets.show', $asset->slug) }}" class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">View public page</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $asset->user->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold
                                            @if($asset->status === 'approved') bg-green-100 text-green-800
                                            @elseif($asset->status === 'rejected') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-3">
                                            @if($asset->status !== 'approved')
                                                <form method="POST" action="{{ route('admin.assets.approve', $asset->id) }}">
                                                    @csrf
                                                    <button class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            @if($asset->status !== 'rejected')
                                                <form method="POST" action="{{ route('admin.assets.reject', $asset->id) }}" class="space-y-2">
                                                    @csrf
                                                    <textarea name="rejection_reason" rows="2" placeholder="Reason for rejection" required class="w-full min-w-64 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('rejection_reason') }}</textarea>
                                                    <button class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                                        Reject
                                                    </button>
                                                </form>
                                            @elseif($asset->rejection_reason)
                                                <p class="max-w-xs text-sm text-red-600">{{ $asset->rejection_reason }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.assets.destroy', $asset->id) }}" onsubmit="return confirm('Delete this asset permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">No assets available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
