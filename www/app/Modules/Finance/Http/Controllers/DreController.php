<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Installment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DreController extends Controller
{
    /**
     * DRE (Demonstração do Resultado do Exercício)
     */
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $dateStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $dateEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Buscamos todas as parcelas "Pagas" dentro do mês de competência (Caixa Realizado)
        // Para um MVP de DRE, podemos usar o Regime de Caixa cruzando paid_date.
        $installments = Installment::where('status', 'PAID')
            ->whereBetween('paid_date', [$dateStart, $dateEnd])
            ->get();

        // 1. Receita Bruta (INCOME)
        $grossIncome = $installments->where('type', 'RECEIVABLE')->sum('amount_cents');

        // 2. CPV / CMV (Custo do Produto Vendido)
        $cogs = $installments->where('dre_category', 'COGS')->sum('amount_cents');
        
        // 3. Impostos Incidentes sobre Venda
        $taxes = $installments->where('dre_category', 'TAXES')->sum('amount_cents');

        // Margem de Contribuição = Receita - CMV - Impostos
        $contributionMargin = $grossIncome - $cogs - $taxes;

        // 4. Despesas Operacionais Fixas (OPEX)
        $opexHr = $installments->where('dre_category', 'OPEX_HR')->sum('amount_cents');
        $opexMarketing = $installments->where('dre_category', 'OPEX_MARKETING')->sum('amount_cents');
        $opexGeneral = $installments->where('dre_category', 'OPEX_GENERAL')->sum('amount_cents');

        $totalOpex = $opexHr + $opexMarketing + $opexGeneral;

        // 5. EBITDA / LAJIDA (Lucro antes dos juros, impostos, depreciação e amortização)
        $ebitda = $contributionMargin - $totalOpex;

        // 6. Fluxo não operacional (CAPEX - Investimentos)
        $capex = $installments->where('dre_category', 'CAPEX')->sum('amount_cents');

        // Lucro Líquido do Exercício (Caixa final)
        $netProfit = $ebitda - $capex;

        return view('finance::dre.index', compact(
            'year', 'month', 'grossIncome', 'cogs', 'taxes', 
            'contributionMargin', 'opexHr', 'opexMarketing', 'opexGeneral', 
            'totalOpex', 'ebitda', 'capex', 'netProfit'
        ));
    }
}
