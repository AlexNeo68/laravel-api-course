<?php

namespace App\Http\Controllers\Api\Product;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\IsDraftMiddleware;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductReview\StoreReviewRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;

class ProductResourceController extends Controller implements HasMiddleware
{

    public function index()
    {
        $products = Product::query()
            ->select(['id', 'name', 'description', 'price', 'count', 'status'])
            ->whereStatus(ProductStatus::Published)
            ->get();

        return $products->map(fn(Product $product) => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'count' => $product->count,
            'status' => $product->status,
            'rating' => $product->getRating(),
        ]);
    }

    public function show(Product $product)
    {
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

    public function destroy(Product $product) {

        $product->delete();

        return response()->json([
            'success' => true
        ])->setStatusCode(Response::HTTP_NO_CONTENT);
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy', 'review_store']),
            new Middleware(IsAdminMiddleware::class, only: ['store', 'update', 'destroy']),
            new Middleware(IsDraftMiddleware::class, only: ['show']),
        ];
    }
}
