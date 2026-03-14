<?php

namespace App\Listeners;

use App\Events\UpdateProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Jobs\Middleware\TenantThrottleMiddleware;


class NotifyAdminProductUpdated implements ShouldQueue
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

        // Example notification
        logger()->info("Admin notified for product {$product->id}");
    }
}
