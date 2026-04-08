<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Settings\Models\Setting;

/**
 * @OA\Tag(
 *     name="Configurações Core",
 *     description="Endpoints de configuração Mestra, Variáveis de Loja, NFC-e e Tema"
 * )
 */
class SettingController extends Controller
{
    /**
     * View do painel Admin de Configurações
     */
    public function index()
    {
        $allSettings = Setting::all()->pluck('value', 'key')->toArray();
        return view('settings::index', compact('allSettings'));
    }

    /**
     * @OA\Post(
     *     path="/api/settings",
     *     tags={"Configurações Core"},
     *     summary="Salvar configurações em Lote",
     *     description="Salva configurações de loja ou sistema mapeando payload (key/value).",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="settings", type="object", example={"store_name": "Minha Loja", "cnpj": "1234"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Configurações Atualizadas e Cache Limpo")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->input('settings', []);
        
        // Handle Certificate Upload
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            // Armazena no storage privado por segurança
            $path = $file->storeAs('fiscal', 'certificado_a1.pfx', 'local');
            $data['fiscal_certificate_path'] = storage_path('app/' . $path);
        }

        foreach ($data as $key => $value) {
            if ($value !== null) {
                // Determine group based on key prefix (optional architectural pattern)
                $group = 'general';
                if (str_starts_with($key, 'fiscal_')) $group = 'fiscal';
                if (str_starts_with($key, 'pos_')) $group = 'pos';
                if (str_starts_with($key, 'ui_')) $group = 'ui';

                Setting::set($key, $value, $group);
            }
        }

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Configurações atualizadas com sucesso']);
        }

        return redirect()->back()->with('success', 'Configurações Globais aplicadas e cacheadas com sucesso!');
    }
}
