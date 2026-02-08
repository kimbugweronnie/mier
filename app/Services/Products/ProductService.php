<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService implements ProductServiceInterface
{
    public function all(): Collection
    {
        // return Product::all();
        foreach (Product::cursor() as $product) {
            // processes one row at a time
        }
        foreach (Product::cursor() as $product) {
            Log::info("Processing product: {$product->name}");
        }
        foreach (Product::cursor() as $product) {
            DB::transaction(function () use ($product) {
                $product->update(['processed_at' => now()]);
                dispatch(new NotifyStockUpdate($product));
            });
        }

        Product::chunk(100, function ($products) {
            foreach ($products as $product) {
                // process
            }
        });
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function find(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
