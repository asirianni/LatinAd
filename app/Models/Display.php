<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\PhotoService;
use Illuminate\Support\Facades\Storage;

class Display extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_per_day',
        'resolution_height',
        'resolution_width',
        'type',
        'user_id',
        'photo_path',
        'photo_thumb_path',
    ];

    protected $casts = [
        'price_per_day' => 'float',
        'resolution_height' => 'integer',
        'resolution_width' => 'integer',
    ];

    /**
     * Relationship with the owner user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for the complete resolution
     */
    public function getResolutionAttribute()
    {
        return "{$this->resolution_width}x{$this->resolution_height}";
    }

    /**
     * Accessor to format the price
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_per_day, 2);
    }

    /**
     * Scope to filter displays for the authenticated user
     */
    public function scopeForUser($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    /**
     * Scope to verify display ownership
     */
    public function scopeOwnedBy($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->id());
    }

    /**
     * Accessor for photo URL
     */
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }
        
        return Storage::disk('public')->url($this->photo_path);
    }

    /**
     * Accessor for thumbnail URL
     */
    public function getPhotoThumbUrlAttribute()
    {
        if (!$this->photo_thumb_path) {
            return null;
        }
        
        return Storage::disk('public')->url($this->photo_thumb_path);
    }

    /**
     * Delete photo files when display is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($display) {
            if ($display->photo_path || $display->photo_thumb_path) {
                $photoService = app(PhotoService::class);
                $photoService->deletePhotoFiles($display->photo_path, $display->photo_thumb_path);
            }
        });
    }
}
