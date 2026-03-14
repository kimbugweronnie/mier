<?php

namespace App\Models;

use App\Casts\PriceCast;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];
    
    // Eager load category relationship,If category is ALWAYS needed:
    protected $with = ['category'];

    protected $casts = [
        'price' => 'integer', // Always integer
        'active' => 'boolean', // Always boolean
        'metadata' => 'array', // JSON <-> array
        'released_at' => 'datetime', // Carbon instance
        'price' => PriceCast::class,

    ];

    // Accessor
    public function getNameAttribute($value)
    {
        return ucwords($value);
    }

    // Mutator
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value);
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }

    // Accessor (read dollars)
    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    protected static function booted()
    {
        static::addGlobalScope(new ActiveScope);
        static::addGlobalScope('tenant', function ($builder) {
            $builder->where('tenant_id', auth()->user()->id);
        });

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
