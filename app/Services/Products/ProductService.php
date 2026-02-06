<?php
namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService implements ProductServiceInterface
{
    public function all(): Collection
    {
        return Product::all();
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