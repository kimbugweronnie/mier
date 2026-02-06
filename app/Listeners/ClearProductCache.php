<?php

namespace App\Listeners;

use App\Events\UpdateProduct;
use Illuminate\Support\Facades\Cache;

class ClearProductCache
{
    public function handle(UpdateProduct $event): void
    {
        $product = $event->product;

        Cache::tags([
            'products',
            "tenant:{$product->tenant_id}"
        ])->flush();

        logger()->info("Product cache cleared: {$product->id}");
    }
}
