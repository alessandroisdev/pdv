<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Settings\Models\StandbyMedia;
use App\Modules\Settings\Models\Setting;
use Illuminate\Support\Facades\Storage;

class StandbyMediaController extends Controller
{
    public function index()
    {
        $medias = StandbyMedia::orderBy('sort_order', 'asc')->get();
        // Fallback for timeout
        $timeout = Setting::where('key', 'standby_timeout_seconds')->value('value') ?? 60;
        
        return view('settings::standby.index', compact('medias', 'timeout'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,mp4|max:50000', // 50MB max
            'duration_seconds' => 'required|integer|min:3',
        ]);

        $path = $request->file('file')->store('standby_medias', 'public');
        $mime = $request->file('file')->getMimeType();
        $type = str_contains($mime, 'video') ? 'VIDEO' : 'IMAGE';

        StandbyMedia::create([
            'type' => $type,
            'file_path' => $path,
            'sort_order' => StandbyMedia::count() + 1,
            'duration_seconds' => $request->duration_seconds,
        ]);

        return redirect()->back()->with('success', 'Mídia de Standby adicionada!');
    }

    public function destroy(StandbyMedia $media)
    {
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        $media->delete();
        return redirect()->back()->with('success', 'Mídia removida com sucesso!');
    }

    public function updateTimeout(Request $request)
    {
        $request->validate(['timeout' => 'required|integer|min:10']);
        
        Setting::updateOrCreate(
            ['key' => 'standby_timeout_seconds'],
            ['value' => $request->timeout]
        );

        return redirect()->back()->with('success', 'Tempo de ociosidade atualizado!');
    }
}
