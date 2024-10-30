<?php

namespace App\Http\Resources\Product;

use App\Models\ProductImage;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShowResource extends JsonResource
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
            'count' => $this->count,
            'price' => $this->price,
            'status' => $this->status,
            'rating' => $this->getRating(),
            'images' => $this->imagesList(),
            'reviews' => ProductReviewIndexResource::collection($this->reviews),
        ];
    }
}
