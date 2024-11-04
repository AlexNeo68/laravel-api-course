<?php

namespace App\Services\Product\DTOs;

use App\Enums\ProductStatus;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProductDTO extends Data
{
    public string $name;

    #[MapInputName('desc')]
    public string|Optional $description;
    public int $count;
    public float $price;

    #[MapInputName('state')]
    public ProductStatus $status;

    public array $images;
}
