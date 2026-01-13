<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Admin – Asset Management
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <!-- Page Container -->
        <div class="bg-white shadow rounded-lg p-6">

            <!-- Table Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-700">All Uploaded Assets</h3>
                <span class="text-gray-500">Total: {{ $assets->count() }}</span>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uploaded By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium">{{ $asset->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $asset->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($asset->status === 'pending')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Approved
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($asset->status === 'pending')
                                        <form method="POST" action="{{ url('/admin/assets/'.$asset->id.'/approve') }}">
                                            @csrf
                                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition duration-150">
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-green-600 font-semibold">✔</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($assets->isEmpty())
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                    No assets available.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
