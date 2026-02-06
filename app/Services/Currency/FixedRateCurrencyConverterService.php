<?php

use App\Services\Currency\CurrencyConverterInterface;

class FixedRateCurrencyConverter implements CurrencyConverterInterface
{
    public function convert(float $amount, string $from, string $to): float
    {
        return $amount * 3800; // fixed UGX rate
    }
}
