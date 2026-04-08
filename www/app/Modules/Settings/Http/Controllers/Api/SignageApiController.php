<?php

namespace App\Modules\Settings\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\Setting;
use App\Modules\Settings\Models\StandbyMedia;

class SignageApiController extends Controller
{
    public function index()
    {
        $timeoutStr = Setting::where('key', 'standby_timeout_seconds')->value('value') ?? '60';
        $timeout = (int) $timeoutStr;

        $mediasQuery = StandbyMedia::orderBy('sort_order', 'asc')->get();

        $medias = $mediasQuery->map(function($media) {
            return [
                'id' => $media->id,
                'type' => $media->type,
                'duration_seconds' => $media->duration_seconds,
                'url' => asset('storage/' . $media->file_path),
            ];
        });

        return response()->json([
            'timeout_seconds' => $timeout,
            'medias' => $medias
        ]);
    }
}
