<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800">
            {{ $asset->title }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
            
            <!-- Preview Image -->
            @if($asset->preview_image)
                <img src="{{ asset('storage/'.$asset->preview_image) }}" 
                     alt="{{ $asset->title }}" 
                     class="w-full h-64 sm:h-80 object-cover">
            @endif

            <div class="p-6">
                <!-- Title & Status -->
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">{{ $asset->title }}</h3>
                    <span class="px-3 py-1 rounded-full text-sm font-medium 
                                 {{ $asset->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($asset->status) }}
                    </span>
                </div>

                <!-- Description -->
                <p class="text-gray-700 mb-6 leading-relaxed">
                    {{ $asset->description }}
                </p>

                <!-- Uploaded By -->
                <p class="text-gray-500 text-sm mb-6">
                    Uploaded by: <span class="font-medium">{{ $asset->user->name }}</span>
                </p>

                <!-- Download Button -->
                <a href="{{ asset('storage/'.$asset->asset_file) }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors duration-300 shadow-md hover:shadow-lg">
                    Download Asset
                </a>

                <!-- Optional: Additional Info -->
                @if($asset->version)
                    <p class="text-gray-400 text-sm mt-4">Version: {{ $asset->version }}</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
