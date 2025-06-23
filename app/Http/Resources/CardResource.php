<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource?->id,
            'name' => $this->resource?->name,
            'slug' => $this->resource?->slug,
            'image' => $this->resource?->image,
            'quantity' => $this->whenLoaded('users', function () use ($request) {
                return $this->resource?->users()->wherePivot('user_id', $request->user()->id)->first()?->pivot->quantity ?? 0;
            })
        ];
    }
}
