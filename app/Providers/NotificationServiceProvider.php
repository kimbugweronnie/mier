<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    
//     ⃣When SHOULD you use deferred providers?
// ✅Good candidates
// ● External API clients (currency, payments, SMS)
// ● Heavy services (PDF generation, reporting)
// ● Services used only in admin or background job

    public function register(): void
    {

        $this->app->when(CheckoutService::class)
                    ->needs(CurrencyConverter::class)
                    ->give(LiveRateConverter::class);
        $this->app->when(AdminReportService::class)
            ->needs(CurrencyConverter::class)
            ->give(FakeRateConverter::class);

    }

    /**
     * Bootstrap services.
     */
    public function provides(): array
    {
        return [
            CurrencyConverter::class,
            LiveRateConverter::class,
            FakeRateConverter::class,
        ];
    }
}
