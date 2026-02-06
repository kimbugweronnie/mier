<?php

namespace App\Providers;

use App\Services\Currency\CurrencyAPIService;
use App\Services\Currency\CurrencyConverterInterface;
use App\Services\Currency\FixedRateCurrencyConverter;
use App\Services\Currency\LiveCurrencyConverter;
use App\Services\Rates\CurrencyConverterService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Event;
use App\Events\PodcastProcessed;
use App\Listeners\SendPodcastNotification;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // config based
        $this->app->singleton(CurrencyConverterService::class, function ($app) {
            return new CurrencyConverterService(config('rates.rates'));
        });

        // dont use singleton for services that have state or are not expensive to create
        $this->app->singleton(CurrencyConverter::class, fn () => new CurrencyConverter);

        // use scoped for better performance and memory management per request
        $this->app->scoped(CurrencyAPIService::class, fn () => new CurrencyAPIService);

        $this->app->scoped(CurrencyConverterInterface::class, LiveCurrencyConverter::class);

        // CONTEXTUAL binding 👇
        $this->app->when(ProductService::class)
            ->needs(CurrencyConverterInterface::class)
            ->give(LiveCurrencyConverter::class);

        $this->app->when(ReportService::class)
            ->needs(CurrencyConverterInterface::class)
            ->give(FixedRateCurrencyConverter::class);
        // or they can be scoped as well if they have state or are not expensive to create
        $this->app->scoped(LiveCurrencyConverter::class);
        $this->app->scoped(FixedRateCurrencyConverter::class);

        // with closure if you need to pass config or other dependencies
        $this->app->scoped(CurrencyConverterInterface::class, function ($app) {
            $config = config('currency.converter');
            if ($config['use_live']) {
                return new LiveCurrencyConverter;
            } else {
                return new FixedRateCurrencyConverter($config['fixed_rate']);
            }
        });
        // with closure if you need to pass config or other dependencies
        $this->app->when(ProductService::class)
            ->needs(CurrencyConverterInterface::class)
            ->give(function ($app) {
                return new LiveCurrencyConverter(
                    cache: $app['cache.store'],
                    http: $app['http']
                );
            });
        // with closure if you need to pass config or other dependencies
        $this->app->when(ProductService::class)
            ->needs(CurrencyConverterInterface::class)
            ->give(function () {
                return app()->environment('production')

                ? new LiveCurrencyConverter
                : new FixedRateCurrencyConverter;
            });

        $this->app->tag([
            LiveCurrencyConverter::class,
            BackupCurrencyConverter::class,
        ], 'currency.providers');

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Event::listen(
            PodcastProcessed::class,
            SendPodcastNotification::class,
        );
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
