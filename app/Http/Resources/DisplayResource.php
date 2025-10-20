<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisplayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price_per_day' => $this->price_per_day,
            'formatted_price' => $this->formatted_price,
            'resolution' => [
                'width' => $this->resolution_width,
                'height' => $this->resolution_height,
                'formatted' => $this->resolution,
            ],
            'type' => $this->type,
            'type_label' => $this->type === 'indoor' ? 'Interior' : 'Exterior',
            'photo_url' => $this->photo_url,
            'photo_thumb_url' => $this->photo_thumb_url,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
