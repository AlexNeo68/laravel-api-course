<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductReview\StoreReviewRequest;
use App\Http\Resources\Product\ProductIndexResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function __construct()
    {
        auth()->login(User::query()->inRandomOrder()->first());
    }

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $products = Product::query()
            ->select(['id', 'name', 'description', 'price', 'count', 'status'])
            ->whereStatus(ProductStatus::Published)
            ->get();

        return ProductIndexResource::collection($products);
    }

    public function show(Product $product)
    {
        if($product->status === ProductStatus::Draft) {
            return response()->json([
                'error' => 'Товар не доступен'
            ], 404);
        }

        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'count' => $product->count,
            'price' => $product->price,
            'status' => $product->status,
            'rating' => $product->getRating(),
            'images' => $product->images->map(fn(ProductImage $productImage) => $productImage->url),
            'reviews' => $product->reviews->map(fn(ProductReview $productReview) => [
                'id' => $productReview->id,
                'author' => $productReview->user->name,
                'text' => $productReview->text,
                'rating' => $productReview->rating,
                'created' => $productReview->created_at->format('Y-m-d h:i'),
            ]),
        ];
    }

    public function store(StoreProductRequest $request){

        $product = auth()->user()->products()->create([
            'name' => $request->str('name'),
            'description' => $request->str('description'),
            'count' => $request->integer('count'),
            'price' => $request->str('price'),
            'status' => $request->enum('status', ProductStatus::class),
        ]);

        if ($request->file('images')){
            foreach($request->file('images') as $file) {

                $url = $file->storePublicly('images');
                if($url) {
                    $product->images()->create([
                        'url' => url($url)
                    ]);
                }

            }
        }


        return response()->json($product, 201);
    }

    public function review_store(StoreReviewRequest $request, Product $product){
        $review = $product->reviews()->create([
            'user_id' => auth()->user()->id,
            'text' => $request->str('text'),
            'rating' => $request->integer('rating'),
        ]);
        return response()->json($review, 201);
    }

    public function update(UpdateProductRequest $request, Product $product): \Illuminate\Http\JsonResponse
    {
        if($request->method() === 'PUT'){
            $product->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'count' => $request->input('count'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
            ]);
        } else {
            $product->update($request->all());
        }
        $product->refresh();
        return response()->json($product, Response::HTTP_ACCEPTED);
    }
}
