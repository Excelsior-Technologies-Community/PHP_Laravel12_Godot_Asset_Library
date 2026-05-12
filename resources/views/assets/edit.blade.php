<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900">Edit Asset</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('assets.update', $asset) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input id="title" name="title" value="{{ old('title', $asset->title) }}" type="text" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select id="category" name="category" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category', $asset->category) === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div>
                            <label for="version" class="block text-sm font-medium text-gray-700">Godot Version</label>
                            <input id="version" name="version" value="{{ old('version', $asset->version) }}" type="text" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <x-input-error :messages="$errors->get('version')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="5" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $asset->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="asset_file" class="block text-sm font-medium text-gray-700">Replace Asset File</label>
                            <input id="asset_file" name="asset_file" type="file" class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Leave empty to keep the current file.</p>
                            <x-input-error :messages="$errors->get('asset_file')" class="mt-2" />
                        </div>

                        <div>
                            <label for="preview_image" class="block text-sm font-medium text-gray-700">Replace Preview Image</label>
                            <input id="preview_image" name="preview_image" type="file" accept="image/*" class="mt-1 w-full rounded-md border border-gray-300 p-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">Leave empty to keep the current preview.</p>
                            <x-input-error :messages="$errors->get('preview_image')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Update Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
