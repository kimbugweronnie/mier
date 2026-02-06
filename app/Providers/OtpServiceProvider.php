<?php

use App\Services\CurrencyConverter;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider implements DeferrableProvider
{
    //     ⃣When SHOULD you use deferred providers?
// ✅Good candidates
// ● External API clients (currency, payments, SMS)
// ● Heavy services (PDF generation, reporting)
// ● Services used only in admin or background job

    public function register()
    {
        $this->app->scoped(CurrencyConverter::class, function ($app) {
            return new CurrencyConverter(
                apiKey: config('services.exchanger.key')
            );
        });
    }

    public function provides(): array
    {
        return [
            CurrencyConverter::class,
        ];
    }
}
