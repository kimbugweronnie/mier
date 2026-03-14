<?php

namespace App\Listeners;

class SendProductNotification
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
    public function handle(ProductCreated $event): void
    {
        $product = Product::find($event->productId);
        Mail::to('admin@example.com')->send(new ProductCreatedMail($product));
    }
}
