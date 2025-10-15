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
     * Relación con el usuario propietario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para la resolución completa
     */
    public function getResolutionAttribute()
    {
        return "{$this->resolution_width}x{$this->resolution_height}";
    }

    /**
     * Accessor para formatear el precio
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price_per_day, 2);
    }
}
