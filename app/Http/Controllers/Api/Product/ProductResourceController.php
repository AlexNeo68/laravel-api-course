<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Middleware\IsAdminMiddleware;
use App\Http\Middleware\IsDraftMiddleware;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductReview\StoreReviewRequest;
use App\Http\Resources\Product\ProductIndexResource;
use App\Http\Resources\Product\ProductShowResource;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Symfony\Component\HttpFoundation\Response;
use App\Facades\Product as ProductFacade;

class ProductResourceController extends Controller implements HasMiddleware
{

    public function index(): AnonymousResourceCollection
    {
        $products = ProductFacade::getPublishedProducts();

        return ProductIndexResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ProductShowResource($product);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {

        $product = ProductFacade::createProduct($request->data());

        return response()->json(new ProductShowResource($product), 201);
    }

    public function review_store(StoreReviewRequest $request, Product $product, ProductService $productService){
        $productService->setProduct($product);
        return response()->json($productService->createReview($request), 201);
    }

    public function update(UpdateProductRequest $request, Product $product, ProductService $productService): JsonResponse
    {
        $productService->setProduct($product);
        $product = $productService->updateProduct($request);
        return response()->json($product, Response::HTTP_ACCEPTED);
    }

    public function destroy(Product $product): JsonResponse
    {

        $product->delete();
        return responseOk()->setStatusCode(Response::HTTP_NO_CONTENT);

//        return response()->json([
//            'success' => true
//        ])->setStatusCode(Response::HTTP_NO_CONTENT);
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
