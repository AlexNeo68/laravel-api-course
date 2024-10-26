<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        auth()->login(User::query()->inRandomOrder()->first());
    }

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

    public function store(Request $request){

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

    public function review_store(Request $request, Product $product){
        $review = $product->reviews()->create([
            'user_id' => auth()->user()->id,
            'text' => $request->str('text'),
            'rating' => $request->integer('rating'),
        ]);
        return response()->json($review, 201);
    }
}
