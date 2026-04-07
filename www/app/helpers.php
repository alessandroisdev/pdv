<?php

use App\Modules\Core\ValueObjects\Money;

if (!function_exists('format_money')) {
    /**
     * Helper to instantiate a Money ValueObject from cents.
     * When echoed, it stringifies symmetrically to "R$ xx,xx".
     *
     * @param int|float|null $cents
     * @return \App\Modules\Core\ValueObjects\Money
     */
    function format_money($cents): Money
    {
        return new Money((int) $cents);
    }
}
