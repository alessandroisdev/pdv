<?php

if (! function_exists('settings')) {
    /**
     * Get a setting value dynamically.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function settings($key, $default = null)
    {
        return \App\Modules\Settings\Models\Setting::getVal($key, $default);
    }
}
