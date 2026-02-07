<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\Product\ProductServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class ProductController extends Controller implements HasMiddleware
{
    public function __construct(
        private ProductServiceInterface $productService
    ) {}

    // public static function middleware(): array
    // {
    //     return [
    //         function (Request $request, Closure $next) {
    //             return $next($request);
    //         },
    //     ];
    // }

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('log', only: ['index']),
            new Middleware('subscribed', except: ['store']),
        ];
    }

    public function index(): View
    {
        $products = $this->productService->all();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $product = $this->productService->find($id);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $product = $this->productService->find($id);
        $this->productService->update($product, $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = $this->productService->find($id);
        $this->productService->delete($product);

        return redirect()
            ->back()
            ->with('success', 'Product deleted successfully');
    }
}
