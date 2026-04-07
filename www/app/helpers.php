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

if (!function_exists('current_pos_actor')) {
    /**
     * Resolves the current actor of the POS Terminal (User vs Physical Employee)
     */
    function current_pos_actor()
    {
        if (request()->is('terminal*')) {
            $employeeId = session('pos_employee_id');
            if (!$employeeId) return null;
            return \App\Modules\AccessControl\Models\Employee::find($employeeId);
        }

        if (auth()->check()) {
            return auth()->user();
        }

        return null; // Force exceptions on checkout
    }
}
