<?php

namespace App\Listeners;

use App\Events\UpdateProduct;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminProductUpdated implements ShouldQueue
{
    public function handle(UpdateProduct $event): void
    {
        $product = $event->product;

        // Example notification
        logger()->info("Admin notified for product {$product->id}");
    }
}
