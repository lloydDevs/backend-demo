<?php

namespace App\Modules\Product\Http\Controllers;

use App\Modules\Core\Http\Controllers\BaseController;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends BaseController
{
    public function __construct(protected ProductService $service)
    {
    }

    public function index(): Response
    {
        $this->authorize('viewAny', Product::class);

        return Inertia::render('Product/Index', [
            'products' => $this->service->listForIndex(10),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Product/Create');
    }

    public function store(StoreProductRequest $request)
    {
        $this->service->createProduct($request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): Response
    {
        $this->authorize('view', $product);

        return Inertia::render('Product/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('Product/Edit', [
            'product' => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->service->updateProduct($product->id, $request->validated());

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $this->service->delete($product->id);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
