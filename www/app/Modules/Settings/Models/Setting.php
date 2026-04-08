<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group'
    ];

    /**
     * Set a setting in the database and flush the specific cache key.
     */
    public static function set($key, $value, $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('settings.' . $key);

        return $setting;
    }

    /**
     * Retrieve a setting from cache or DB.
     * Use Global function helper if available.
     */
    public static function getVal($key, $default = null)
    {
        return Cache::rememberForever('settings.' . $key, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Flush all settings cache keys.
     */
    public static function flushCache()
    {
        // Simple way to ensure fresh settings is to retrieve all keys and forget them
        $keys = self::pluck('key')->toArray();
        foreach ($keys as $key) {
            Cache::forget('settings.' . $key);
        }
    }
}
