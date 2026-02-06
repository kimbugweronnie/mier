<?php

namespace App\Services\Rates;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyConverter
{
    protected array $rates;

    public function __construct()
    {
        $rates = $this->fetchRates();
        // $this->rates = Cache::remember(
        //     'exchange_rates',
        //     now()->addMinutes(30),
        //     fn () => $rates
        // );
    }

    protected function fetchRates(): array
    {
        // temporary fallback
        return [
            'USD' => 1,
            'UGX' => 3800,
            'EUR' => 0.92,
        ];
    }

    protected function fetchRatesLive(): array
    {
        $response = Http::timeout(5)
            ->get('https://api.exchangerate.host/latest', [
                'base' => 'USD',
            ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Failed to fetch exchange rates');
        }

        return $response->json('rates');
    }

    public function convert(float $amount, string $from, string $to): float
    {
        if (! isset($this->rates[$from], $this->rates[$to])) {
            throw new \InvalidArgumentException('Unsupported
currency');
        }
        $usd = $amount / $this->rates[$from];

        return round($usd * $this->rates[$to], 2);
    }
}
