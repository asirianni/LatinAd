<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
