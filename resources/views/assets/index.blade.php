<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Godot Asset Library
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-800 p-4 mb-6 rounded-lg shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Asset Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($assets as $asset)
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-gray-200 overflow-hidden">
                    <!-- Optional Preview Image -->
                    @if($asset->preview_image)
                        <img src="{{ asset('storage/'.$asset->preview_image) }}" alt="{{ $asset->title }}" class="w-full h-48 object-cover">
                    @endif

                    <div class="p-5 flex flex-col h-full">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $asset->title }}</h3>

                        <p class="text-gray-600 flex-grow">
                            {{ Str::limit($asset->description, 120) }}
                        </p>

                        <div class="mt-4 flex items-center justify-between">
                            <a href="{{ url('/assets/'.$asset->slug) }}" class="text-blue-600 font-semibold hover:underline">
                                View Details →
                            </a>
                            <span class="text-sm text-gray-500 uppercase">{{ $asset->status }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center col-span-full">No assets available.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
