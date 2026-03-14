<?php

class PriceCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new Price((int) $value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return [$key => $value instanceof Price ? $value->amount : (int) $value];
    }
}
