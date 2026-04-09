<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use App\Modules\Finance\Models\Transaction;

/**
 * @OA\Tag(
 *     name="Financeiro",
 *     description="Operações Financeiras, Tesouraria e Dashboard Analytics"
 * )
 */
class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/financeiro/dashboard/data",
     *     tags={"Financeiro"},
     *     summary="Obter Dados do Dashboard",
     *     description="Retorna as métricas agregadas do financeiro e os últimos alertas de auditoria.",
     *     @OA\Response(
     *         response=200,
     *         description="Métricas JSON"
     *     )
     * )
     */
    public function index()
    {
        // Buscar Receitas e Despesas Reais
        $income = Transaction::where('type', 'INCOME')->whereMonth('created_at', now()->month)->sum('amount_cents');
        $expense = Transaction::where('type', 'EXPENSE')->whereMonth('created_at', now()->month)->sum('amount_cents');
        
        $totalIncome = Transaction::where('type', 'INCOME')->sum('amount_cents');
        $totalExpense = Transaction::where('type', 'EXPENSE')->sum('amount_cents');
        $cashBalance = $totalIncome - $totalExpense;
        
        // Como a tabela não possui 'category', calculamos como uma proporção ou deixamos zerado e mostramos o valor total de saídas no sistema neste cartão caso não haja integração com Contas a Pagar finalizada.
        // Simulando/Calculando custos da folha se a feature não estiver mapeada
        $fixedCosts = Transaction::where('type', 'EXPENSE')
            ->whereMonth('created_at', now()->month)
            ->sum('amount_cents');

        // Formatar para decimais
        $metrics = [
            'cash_balance' => $cashBalance / 100, // Saldo Caixa R$
            'monthly_incomes' => $income / 100,
            'monthly_expenses' => $expense / 100,
            'fixed_costs' => $fixedCosts / 100,
        ];

        // Buscar Alertas de Auditoria Reais via owen-it/laravel-auditing
        // Filtrando para exclusões críticas recentes
        $criticalAudits = Audit::where('event', 'deleted')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Gráfico de Fluxo de Caixa Diário (Últimos 7 dias reais do banco)
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = \Carbon\Carbon::today()->subDays($i);
            $inc = Transaction::where('type', 'INCOME')->whereDate('created_at', $date)->sum('amount_cents');
            $exp = Transaction::where('type', 'EXPENSE')->whereDate('created_at', $date)->sum('amount_cents');
            $last7Days->push([
                'date' => $date->format('d/m'),
                'income' => $inc / 100,
                'expense' => $exp / 100
            ]);
        }
        
        $chartData = [
            'labels' => $last7Days->pluck('date')->toArray(),
            'income' => $last7Days->pluck('income')->toArray(),
            'expense' => $last7Days->pluck('expense')->toArray()
        ];

        return view('finance::dashboard', compact('metrics', 'criticalAudits', 'chartData'));
    }
}
