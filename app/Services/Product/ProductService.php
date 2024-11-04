<?php

namespace App\Services\Product;

use AllowDynamicProperties;
use App\Enums\ProductStatus;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductReview\StoreReviewRequest;
use App\Models\Product;
use App\Services\Product\DTOs\ProductDTO;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

#[AllowDynamicProperties] class ProductService
{

    private Product $product;

    public function getPublishedProducts():Collection
    {
        return Product::query()
            ->select(['id', 'name', 'description', 'price', 'count', 'status'])
            ->whereStatus(ProductStatus::Published)
            ->get();
    }

    public function createProduct(ProductDTO $data): Product
    {
        $images = Arr::get($data->toArray(), 'images');

        $product = auth()->user()->products()->create($data->except('images')->toArray());

        if ($images){
            foreach($images as $file) {

                $url = $file->storePublicly('images');
                if($url) {
                    $product->images()->create([
                        'url' => url($url)
                    ]);
                }

            }
        }

        return $product;
    }

    public function updateProduct(UpdateProductRequest $request): Product
    {
        if($request->method() === 'PUT'){
            $this->product->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'count' => $request->input('count'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
            ]);
        } else {
            $this->product->update($request->all());
        }
        $this->product->refresh();

        return $this->product;
    }


    public function createReview(StoreReviewRequest $request)
    {
        return $this->product->reviews()->create([
            'user_id' => auth()->user()->id,
            'text' => $request->str('text'),
            'rating' => $request->integer('rating'),
        ]);

    }


    public function setProduct(Product $product)
    {
        $this->product = $product;
    }
}
