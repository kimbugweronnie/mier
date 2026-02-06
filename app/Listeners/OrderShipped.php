<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Middleware\RateLimited;


class SendShipmentNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(OrderShipped $event): void
    {
        // Access the order using $event->order...
    }

    public function middleware(OrderShipped $event): array
    {
        return [new RateLimited];
    }

    // Optional: conditionally queue the listener
    public function shouldQueue(OrderShipped $event): bool
    {
        return $event->order->subtotal >= 5000;
    }
}

// class SendShipmentNotification implements ShouldQueue
// {
//     /**
//      * The name of the connection the job should be sent to.
//      *
//      * @var string|null
//      */
//     public $connection = 'sqs';

//     /**
//      * The name of the queue the job should be sent to.
//      *
//      * @var string|null
//      */
//     public $queue = 'listeners';

//     /**
//      * The time (seconds) before the job should be processed.
//      *
//      * @var int
//      */
//     public $delay = 60;
// }
