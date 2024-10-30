<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'count',
        'price',
        'status',
    ];

    protected $casts = [
        'status' => ProductStatus::class
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviews():HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function images():HasMany
    {
        return $this->hasMany(ProductImage::class)->select('url');
    }

    public function getRating(): int|float
    {
        return $this->reviews()->count() ? round($this->reviews()->avg('rating'), 1) : 0;
    }

    public function imagesList()
    {
        return $this->images->map(fn(ProductImage $productImage) => $productImage->url);
    }


    public function isDraft(): bool
    {
        return $this->status === ProductStatus::Draft;
    }
}
