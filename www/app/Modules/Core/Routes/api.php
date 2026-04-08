<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Settings\Models\StandbyMedia;
use App\Modules\Settings\Models\Setting;

Route::middleware('api')->prefix('api/v1')->group(function () {
    Route::get('/signage', function () {
        $medias = StandbyMedia::orderBy('sort_order', 'asc')->get()->map(function ($media) {
            return [
                'id' => $media->id,
                'type' => $media->type,
                'url' => asset('storage/' . $media->file_path),
                'duration_seconds' => $media->duration_seconds,
            ];
        });

        $timeout = Setting::where('key', 'standby_timeout_seconds')->value('value') ?? 60;

        return response()->json([
            'timeout_seconds' => (int) $timeout,
            'medias' => $medias
        ]);
    });
});
