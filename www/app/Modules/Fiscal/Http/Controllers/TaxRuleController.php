<?php

namespace App\Modules\Fiscal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Fiscal\Models\TaxRule;

class TaxRuleController extends Controller
{
    /**
     * Interface gerencial do Contabilista
     */
    public function index()
    {
        return view('fiscal::settings.taxes');
    }

    public function datatable(Request $request)
    {
        $query = TaxRule::select('tax_rules.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['ncm', 'fiscal_regime', 'cfop'],
                function ($rule) {
                    $ncm = $rule->ncm;
                    
                    // FALLBACK: Se não tiver NCM, ela marca a regra principal geral "Fallback/Generic"
                    if (!$ncm || empty(trim($ncm))) {
                        $ncmBag = "<span style='padding: 0.25rem 0.5rem; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 0.75rem; font-weight: bold;'><i class='fa fa-globe'></i> REGRA GERAL (SEM NCM)</span>";
                    } else {
                        $ncmBag = "<span style='font-family: monospace; font-weight: bold; font-size: 0.85rem; color: #1e293b;'>{$ncm}</span>";
                    }

                    $regime = "<span style='font-size: 0.75rem; color: #64748b;'>{$rule->fiscal_regime}</span>";
                    
                    // Format ICMS and CSOSN
                    $icms = "<span style='font-weight: bold; color: #0f172a;'>CSOSN: {$rule->cst_csosn}</span><br><span style='font-size: 0.75rem; color: #475569;'>ICMS (%): " . number_format($rule->icms_rate, 2, ',', '.') . "</span>";
                    
                    // Format CFOP
                    $cfop = "<span style='font-family: monospace; font-size: 0.85rem; color: #334155;'>{$rule->cfop}</span>";

                    // Active Toggle
                    $status = $rule->is_active 
                        ? "<span style='color: #10b981;'><i class='fa fa-check-circle'></i> Ativa</span>"
                        : "<span style='color: #ef4444;'><i class='fa fa-times-circle'></i> Inativa</span>";

                    return [
                        'ncm' => $ncmBag . "<br>" . $regime,
                        'cfop' => $cfop,
                        'icms' => $icms,
                        'status' => $status
                    ];
                }
            )
        );
    }

    /**
     * Store new Tax Rule
     */
    public function store(Request $request)
    {
        // Regras restritas apenas ao Contador/Admin (Validado no Midleware global, mas o form vem limpo)
        $request->validate([
            'ncm' => 'nullable|string|max:10',
            'cfop' => 'required|string|max:4',
            'cst_csosn' => 'required|string|max:4',
            'icms_rate' => 'required|numeric|min:0|max:100',
            'fiscal_regime' => 'required|string',
            'pis_rate' => 'nullable|numeric|min:0|max:100',
            'cofins_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $ncm = $request->input('ncm');
        $ncmClean = $ncm ? trim(str_replace('.', '', $ncm)) : null;

        // Validar Fallback Único Mestre: Se já tem regra geral pro Simples sem NCM, bloqueia se a UF for vazia tmb (MVP)
        if (!$ncmClean) {
            $exists = TaxRule::whereNull('ncm')
                             ->where('fiscal_regime', $request->input('fiscal_regime'))
                             ->exists();
                             
            if ($exists) {
                return redirect()->back()
                                 ->withInput()
                                 ->withErrors(['ncm' => 'Já existe uma REGRA GERAL (Sem NCM) cadastrada para este Regime Tributário. Por favor, especifique o NCM.']);
            }
        }

        TaxRule::create([
            'fiscal_regime' => $request->input('fiscal_regime'),
            'ncm' => $ncmClean, // Se nulo, Motor entende como Fallback
            'cfop' => $request->input('cfop'),
            'cst_csosn' => $request->input('cst_csosn'),
            'icms_rate' => $request->input('icms_rate'),
            'pis_rate' => $request->input('pis_rate') ?? 0,
            'cofins_rate' => $request->input('cofins_rate') ?? 0,
            'is_active' => true
        ]);

        return redirect()->route('fiscal.settings.taxes')->with('success', 'Regra Tributária mapeada ao Motor Nfe com sucesso!');
    }
}
