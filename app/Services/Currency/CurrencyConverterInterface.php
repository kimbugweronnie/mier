<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface CurrencyConverterInterface
{
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float;
}
