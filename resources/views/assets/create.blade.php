<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800">
            Upload New Asset
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto">
        <!-- Form Container -->
        <div class="bg-white shadow-lg rounded-lg p-8 border border-gray-200">
            
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 mb-6 rounded-lg border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Upload Form -->
            <form method="POST" action="{{ url('/upload') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Title -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Title</label>
                    <input type="text" name="title" 
                           class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:outline-none"
                           placeholder="Enter asset title" required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" rows="5"
                              class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-400 focus:outline-none"
                              placeholder="Write a brief description of your asset" required></textarea>
                </div>

                <!-- Asset File -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Asset File</label>
                    <input type="file" name="asset_file" 
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-green-400 focus:outline-none"
                           required>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md transition duration-150">
                        Submit for Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
