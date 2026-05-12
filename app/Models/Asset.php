<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'version',
        'asset_file',
        'preview_image',
        'downloads',
        'rejection_reason',
        'user_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPreviewUrlAttribute(): ?string
    {
        if (!$this->preview_image || !Storage::disk('public')->exists($this->preview_image)) {
            return null;
        }

        return Storage::url($this->preview_image);
    }
}
