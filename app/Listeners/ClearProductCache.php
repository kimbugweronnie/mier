<?php

namespace App\Listeners;

use App\Events\UpdateProduct;
use Illuminate\Support\Facades\Cache;
use App\Jobs\Middleware\TenantThrottleMiddleware;


class ClearProductCache
{
    public function middleware(): array
    {
        return [
            new TenantThrottleMiddleware
        ];
    }
    
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
