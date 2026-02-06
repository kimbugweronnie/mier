<?php

namespace App\Listeners;

class UpdateProject
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */

    public function middleware(): array
    {
        return [
            new TenantThrottleMiddleware
        ];
    }
    public function handle(OrderCreated $event)
    {
        Product::whereId($event->order->product_id)
            ->decrement('stock', $event->order->quantity);
    }
}
