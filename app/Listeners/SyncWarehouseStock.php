<?php
namespace App\Listeners;

use App\Events\UpdateProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Middleware\TenantThrottleMiddleware;


class SyncWarehouseStock implements ShouldQueue
{
    public int $tries = 3;
    public int $backoff = 10;

    public function middleware(): array
    {
        return [
            new TenantThrottleMiddleware
        ];
    }

    public function handle(UpdateProduct $event): void
    {
        $product = $event->product;

        // Simulate external warehouse API
        logger()->info("Warehouse synced for product {$product->id}");
    }
}
