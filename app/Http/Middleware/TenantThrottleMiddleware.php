<?php

namespace App\Jobs\Middleware;

use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Closure;


class TenantThrottleMiddleware
{
    public function handle($job, Closure $next)
    {
        $tenantId = $job->event->product->tenant_id;

        Redis::throttle("tenant:{$tenantId}:product-updates")
            ->allow(5)        // 5 jobs
            ->every(60)       // per minute
            ->then(function () use ($job, $next) {

                $next($job);

            }, function () use ($job) {

                // Release job back to queue
                $job->release(10);
            });
    }
}
