<?php

namespace App\Modules\Fiscal\Services;

use App\Modules\Fiscal\Models\TaxRule;
use App\Modules\Inventory\Models\Product;

class TaxEngine
{
    /**
     * Calcula as diretrizes fiscais para um produto vendido do estado X pro Y.
     */
    public function computeTaxes(Product $product, string $originUF, string $destinationUF)
    {
        // 1. Tentar achar regra específica para NCM cravado na rota
        $rule = TaxRule::where('is_active', true)
            ->where('ncm', $product->barcode_ncm) // assumindo campo NCM ex: 48201000
            ->where('uf_origin', $originUF)
            ->where('uf_destination', $destinationUF)
            ->first();

        // 2. Fallback Estadual Genérico (Sem NCM específico, apenas Interestadual)
        if (!$rule) {
            $rule = TaxRule::where('is_active', true)
                ->whereNull('ncm')
                ->where('uf_origin', $originUF)
                ->where('uf_destination', $destinationUF)
                ->first();
        }

        // 3. Fallback Intra-estado nativo genérico
        if (!$rule) {
            $rule = TaxRule::where('is_active', true)
                ->whereNull('ncm')
                ->whereNull('uf_origin')
                ->whereNull('uf_destination')
                ->first();
        }

        // 4. Default Hardcoded de Segurança Legal MVP (SIMPLES NACIONAL 102/502) se não achar nada
        if (!$rule) {
            return [
                'cfop' => ($originUF === $destinationUF) ? '5102' : '6102',
                'cst_csosn' => '0102',
                'icms_rate' => 0.0,
                'has_st' => false
            ];
        }

        // Retornar a diretriz encontrada
        return [
            'cfop' => $rule->cfop ?? (($originUF === $destinationUF) ? '5102' : '6102'),
            'cst_csosn' => $rule->cst_csosn ?? '0102',
            'icms_rate' => clone $rule->icms_rate,
            'icms_st_margin' => $rule->icms_st_margin,
            'pis_rate' => $rule->pis_rate,
            'cofins_rate' => $rule->cofins_rate,
            'has_st' => (bool)$rule->has_st
        ];
    }
}
