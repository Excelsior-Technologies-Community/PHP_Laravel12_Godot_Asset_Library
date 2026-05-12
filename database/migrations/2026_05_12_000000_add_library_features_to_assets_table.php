<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->string('preview_image')->nullable()->after('asset_file');
            $table->unsignedInteger('downloads')->default(0)->after('preview_image');
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'preview_image',
                'downloads',
                'rejection_reason',
            ]);
        });
    }
};
