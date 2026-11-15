<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ref' => $this->ref, 'title' => $this->displayTitle(), 'category' => $this->category?->key, 'price' => $this->price, 'city' => $this->city,
            'age' => $this->age, 'breed' => $this->breed, 'weight' => $this->weight, 'description' => $this->descr, 'verified' => $this->verified,
            'images' => $this->images->map(fn ($i) => asset('storage/'.$i->path)), 'approved_at' => $this->approved_at?->toIso8601String(),
        ];
    }
}
