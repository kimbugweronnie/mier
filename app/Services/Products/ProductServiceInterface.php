<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function all(): Collection;

    public function create(array $data): Product;

    public function find(int $id): Product;

    public function update(Product $product, array $data): Product;

    public function delete(Product $product): void;
}
