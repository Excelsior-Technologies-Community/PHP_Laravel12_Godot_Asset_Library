# PHP_Laravel12_Godot_Asset_Library


## Introduction

The Godot Asset Library is a web-based asset management system built using Laravel 12.
It allows users to upload, view, and download assets, while admins can approve submitted assets before they are publicly available.

---

## Project Overview

Users can register/login using Laravel Breeze.

Authenticated users can upload assets for review.

Admin users can approve or pending assets.

Public users can browse and download approved assets.

The project uses Laravel 12, MySQL, Tailwind CSS, and Blade templates.

---

## Features

User registration & authentication

Role-based access (admin vs user)

Asset upload (file + description + version)

Admin approval workflow

Public listing of approved assets

Download functionality

Responsive design using Tailwind CSS

---

##  Tech Stack

- **Laravel 12**
- **PHP 8.2+**
- **MySQL**
- **Blade Templates**
- **Bootstrap 5**
- **Laravel Breeze (Authentication)**

---

## Step 1: Project Creation (Laravel 12)

```bash
composer create-project laravel/laravel PHP_Laravel12_Godot_Asset_Library "12.*"
cd PHP_Laravel12_Godot_Asset_Library
```

Start development server:
```bash
php artisan serve
```

---

##  Step 2: Authentication Setup

Install Laravel Breeze for login and registration.

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan migrate
npm install
npm run dev
```

---

##  Step 3: Database Setup


```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=godot_asset_library
DB_USERNAME=root
DB_PASSWORD=
```

After run this command:

```bash
php artisan migrate
```

---

## Step 4: Migration table

###  Users Table (Default)

- id
- name
- email
- password

### Assets Table

```bash
php artisan make:migration create_assets_table
```

File: database/migrations/xxxx_xx_xx_xxxxxx_create_assets_table.php


```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('version')->nullable();
            $table->string('asset_file');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
```

### Create migration to add is_admin to users 

```bash
php artisan make:migration add_is_admin_to_users_table --table=users
```

File: database/migrations/xxxx_xx_xx_xxxxxx_add_is_admin_to_users_table.php


```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(0)->after('email');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
```

Run migrations:
```bash
php artisan migrate
```

---

## Step 5: Model

```bash
php artisan make:model Asset
```

File: app/Models/Asset.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'version',
        'asset_file',
        'preview_image',
        'user_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## Step 5: Controllers

```bash
php artisan make:controller AssetController
php artisan make:controller Admin/AdminAssetController
```

### 5.1) Asset Controller

File: app/Http/Controllers/AssetController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::where('status', 'approved')->latest()->get();
        return view('assets.index', compact('assets'));
    }

    public function show($slug)
    {
        $asset = Asset::where('slug', $slug)->firstOrFail();
        return view('assets.show', compact('asset'));
    }

    public function create()
    {
        return view('assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'asset_file' => 'required|file'
        ]);

        $filePath = $request->file('asset_file')
                            ->store('assets', 'public');

        Asset::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'asset_file' => $filePath,
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect('/')->with('success', 'Asset uploaded for review');
    }
}
```

### 5.2) Admin Asset Controller

File: app/Http/Controllers/Admin/AdminAssetController.php

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;

class AdminAssetController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->get();
        return view('admin.assets.index', compact('assets'));
    }

    public function approve($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->status = 'approved';
        $asset->save();

        return back();
    }
}
```

---

## Step 6: Admin Middleware

```bash
php artisan make:middleware AdminMiddleware
```

File: app/Http/Middleware/AdminMiddleware.php

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->is_admin != 1) {
            abort(403);
        }
        return $next($request);
    }
}
```

---


## Step 7: bootstrap/app.php

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

---

## Step 8: Views (Blade)

### 8.1) app.blade.php

File: resources/views/layouts/app.blade.php

```
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
```

### 8.2) index.blade.php

File: resources/views/assets/index.blade.php

```
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
```

### 8.3) create.blade.php

File: resources/views/assets/create.blade.php

```
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
```

### 8.4) show.blade.php

File: resources/views/assets/show.blade.php

```
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
```

### 8.5) index.blade.php (Admin)

File: resources/views/admin/assets/index.blade.php

```
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
```

---

## Step 9: Web.php

File: routes/web.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Admin\AdminAssetController;

/*
|--------------------------------------------------------------------------
| Public Asset Routes
|--------------------------------------------------------------------------
| Anyone can view approved assets
*/

Route::get('/', [AssetController::class, 'index'])
    ->name('home');

Route::get('/assets/{slug}', [AssetController::class, 'show'])
    ->name('assets.show');

/*
|--------------------------------------------------------------------------
| Default Dashboard (Laravel Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])
  ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Profile (Breeze default)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // Asset Upload
    Route::get('/upload', [AssetController::class, 'create'])
        ->name('assets.create');

    Route::post('/upload', [AssetController::class, 'store'])
        ->name('assets.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Only logged-in admins can access
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/assets', [AdminAssetController::class, 'index'])
            ->name('admin.assets.index');

        Route::post('/assets/{id}/approve', [AdminAssetController::class, 'approve'])
            ->name('admin.assets.approve');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
```

---

Step 10: Storage Link Setup (Important)

This project allows users to upload assets (files) which are stored using Laravel’s public disk.

Laravel stores uploaded files inside:

```
storage/app/public
```

To make these files accessible from the browser (for preview and download), we must create a symbolic link to the public directory.

Run the following command:

```bash
php artisan storage:link
```

---

## Project Structure

```
PHP_Laravel12_Godot_Asset_Library
│
├── app
│   └── Http
│       ├── Controllers
│       │   ├── AssetController.php
│       │   └── Admin
│       │       └── AdminAssetController.php
│       │
│       └── Middleware
│           └── AdminMiddleware.php
│
├── database
│   └── migrations
│       ├── xxxx_xx_xx_create_users_table.php
│       ├── xxxx_xx_xx_add_is_admin_to_users_table.php
│       └── xxxx_xx_xx_create_assets_table.php
│
├── app
│   └── Models
│       ├── Asset.php
│       └── User.php
│
├── bootstrap
│   └── app.php
│
├── resources
│   └── views
│       ├── layouts
│       │   └── app.blade.php
│       │
│       ├── assets
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   └── create.blade.php
│       │
│       └── admin
│           └── assets
│               └── index.blade.php
│
├── routes
│   └── web.php
│
├── public
│   └── storage
│       └── assets
│
├── storage
│   └── app
│       └── public
│           └── assets
│
├── .env
├── .env.example
├── artisan
├── composer.json
├── package.json
└── README.md
```

---

## Step 12: Admin User via Tinker

This project uses a simple admin role system based on the is_admin column in the users table.

## Option 1: Promote Existing User to Admin (Manual via Tinker) 

Use Laravel Tinker to promote an already registered user to admin.

```
php artisan tinker
```

Run the following commands:

```
$user = App\Models\User::where('email', 'demo@123gmail.com')->first();
$user->is_admin = 1;
$user->save();
```

Option 2: Create Admin User via Tinker

Run:

```
php artisan tinker
```

Then execute:

```
use App\Models\User;

User::create([
    'name' => 'Admin',
    'email' => 'xxxxx',
    'password' => bcrypt('xxxx'),
    'is_admin' => 1,
]);
```

Exit tinker:

```
exit
```

Login using:

```
Email: xxxxx
Password: xxxx
```
Admin Access URL

After logging in as admin, access the admin panel:

```
http://127.0.0.1:8000/admin/assets
```

Only users with is_admin = 1 can access this route.


---

## Step 13: Start Development Environment

After completing all setup steps, start the application.

### Start Laravel Development Server

```bash
php artisan serve
```

By default, the application will be available at:

```bash
http://127.0.0.1:8000
```

### Start Frontend (Vite)

In a new terminal, run:

```bash
npm run dev
```

This compiles Tailwind CSS and frontend assets.

Important:

Both commands must be running during development:

```bash
php artisan serve
npm run dev
```

---

## Output
 
<img width="1919" height="1027" alt="Screenshot 2026-01-13 114945" src="https://github.com/user-attachments/assets/8844d388-c255-4798-aac2-318a1e04594e" />

<img width="1919" height="1028" alt="Screenshot 2026-01-13 114954" src="https://github.com/user-attachments/assets/8246950d-78d9-402c-a8c7-9bfd62082e64" />

<img width="1919" height="1032" alt="Screenshot 2026-01-13 115050" src="https://github.com/user-attachments/assets/ee82a5b6-676e-49c0-b8c0-caba9b581f02" />

<img width="1919" height="1031" alt="Screenshot 2026-01-13 115101" src="https://github.com/user-attachments/assets/ed35baec-3d69-4942-9ecc-608bab03fd6f" />

<img width="1919" height="1030" alt="Screenshot 2026-01-13 115116" src="https://github.com/user-attachments/assets/5d8dc979-d082-4643-b1da-e5d18e500b76" />

<img width="1919" height="1028" alt="Screenshot 2026-01-13 115125" src="https://github.com/user-attachments/assets/f6bd373c-4d1f-4e03-b55a-a92a1d5e32f4" />

---

Your PHP_Laravel12_Godot_Asset_Library Project is Now Ready!
