<?php

class FakeCurrencyConverter implements CurrencyConverterInterface
{
    public function convert(float $amount, string $from, string $to): float
    {
        return 1000; // predictable
    }
}
