<?php

namespace App\Services\Currency;
class CurrencyConverter
{
    protected array $rates;
    public function __construct(array $rates)
    {
    $this->rates = $rates;
    }
    public function convert(float $amount,string $from,string $to): float {
        if ($from === $to) {
            return $amount;
        }
        if (! isset($this->rates[$from], $this->rates[$to])) {
            throw new \InvalidArgumentException('Unsupported
            currency');
        }
    // normalize to base (USD for example)
    $usdAmount = $amount / $this->rates[$from];
    return round($usdAmount * $this->rates[$to], 2);
    }
}