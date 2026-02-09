<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);

        static::creating(function (Product $product) {
            $product->uuid = Str::uuid();
        });
        static::updated(function (Product $product) {
            Cache::forget('products');
        });
        static::deleted(function (Product $product) {
            Cache::forget('products');
        });
    }

    // LOCAL SCOPE
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeExpensive($query, $price)
    {
        return $query->where('price', '>', $price);
    }

    public function getAll(): Collection
    {
        // Using local scope
        $activeProducts = $this::active()->scopeExpensive(1000)->get();

        // Removing global scope
        $allProducts = $this::withoutGlobalScope(ActiveScope::class)->get();

        return $allProducts;

        return $this::latest()->get();
    }

    public function findByName(string $name): ?self
    {
        return $this::where('name', $name)->first();
    }

    public function findById(int $id): self
    {
        return $this::where('id', $id)->firstOrFail();
    }

    public function store(array $data): self
    {
        return $this::create($data);
    }

    public function updateProduct(array $data): self
    {
        $this::update($data);

        return $this;
    }

    public function deleteProduct(): bool
    {
        return $this::delete();
    }
}
