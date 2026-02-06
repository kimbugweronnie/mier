<?php

namespace App\Providers;

use App\Events\PodcastProcessed;
use App\Models\Employee;
use App\Observers\EmployeeObserver;
use App\Services\Product\EmployeeService;
use App\Services\Product\EmployeeServiceInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

use function Illuminate\Events\queueable;

class EmployeeProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        Employee::observe(EmployeeObserver::class);
        //         ✅Model observers
        // ✅Global scopes
        // ✅Route macros
        // ✅Event listeners
        // ✅View composers
        // Global scope example
        Employee::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', true);
        });

        // When registering closure-based event listeners, you may wrap the listener closure within the Illuminate\Events\queueable
        // function to instruct Laravel to execute the listener using the queue:
        Event::listen(queueable(function (PodcastProcessed $event) {
            // ...
        }));

        // Like queued jobs, you may use the onConnection, onQueue,
        // and delay methods to customize the execution of the queued listener:

        // Event::listen(queueable(function (PodcastProcessed $event) {
        //     // ...
        // })->onConnection('redis')->onQueue('podcasts')->delay(now()->add(seconds: 10)));

        // If you would like to handle anonymous queued listener failures, you may provide a closure to the catch method while defining the queueable listener. This closure will receive 
        // the event instance and the Throwable instance that caused the listener's failure:

        Event::listen(queueable(function (PodcastProcessed $event) {
            // ...
        })->catch(function (PodcastProcessed $event, Throwable $e) {
            // The queued listener failed...
        }));

    }
}
