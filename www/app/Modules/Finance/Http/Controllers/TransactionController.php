<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Transaction;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Gestão Financeira",
 *     description="Operações de Caixa, Extratos e Exportações"
 * )
 */
class TransactionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/finance/transactions",
     *     tags={"Gestão Financeira"},
     *     summary="Extrato Financeiro e Dashboard",
     *     description="Consulta as transações globais e obtém os agregados de faturamento.",
     *     @OA\Response(response=200, description="Payload de Transações e Métricas")
     * )
     */
    public function index(Request $request)
    {
        // Balance Sum
        $income = Transaction::where('type', 'INCOME')->sum('amount_cents');
        $expense = Transaction::where('type', 'EXPENSE')->sum('amount_cents');
        $balanceCents = $income - $expense;

        // Metrics Advanced
        $todayIncome = Transaction::where('type', 'INCOME')->whereDate('created_at', now()->today())->sum('amount_cents');
        $ticketMedio = 0;
        $vendasCount = \App\Modules\Sales\Models\Sale::whereDate('created_at', now()->today())->count();
        if ($vendasCount > 0) {
            $ticketMedio = $todayIncome / $vendasCount;
        }

        // Audits (Quebras de Caixa)
        $caixasComDivergencia = \App\Modules\Sales\Models\CashRegister::where('difference_cents', '!=', 0)->latest()->take(5)->get();

        if ($request->wantsJson() || $request->is('api/*')) {
            $transactions = Transaction::with(['actor', 'source'])->latest()->paginate(20);
            return response()->json([
                'balance_cents' => $balanceCents,
                'metrics' => [
                    'income' => $income,
                    'expense' => $expense,
                    'today_income' => $todayIncome,
                    'average_ticket' => $ticketMedio
                ],
                'divergences' => $caixasComDivergencia,
                'transactions' => $transactions->items()
            ]);
        }

        return view('finance::transactions.index', compact('balanceCents', 'income', 'expense', 'todayIncome', 'ticketMedio', 'caixasComDivergencia'));
    }

    public function datatable(Request $request)
    {
        $query = Transaction::with(['actor', 'source'])->select('transactions.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['description', 'payment_method', 'category'],
                function ($tx) {
                    $dateIdHtml = "<strong class='text-indigo-600'>#" . str_pad($tx->id, 5, '0', STR_PAD_LEFT) . "</strong><br><span class='text-xs'>{$tx->created_at->format('d/m/Y H:i')}</span>";

                    $tipoBadge = $tx->type == 'INCOME'
                        ? "<span class='inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700'>ENTRADA</span>"
                        : "<span class='inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-rose-100 text-rose-700'>SAÍDA</span>";
                    $tipoHtml = "{$tipoBadge}<div class='text-xs font-bold text-slate-400 mt-1 uppercase'>VIA " . ($tx->payment_method ?? 'ND') . "</div>";

                    if ($tx->source_type === \App\Modules\Sales\Models\Sale::class) {
                        $origemHtml = "<div class='flex items-center gap-2'><div class='bg-slate-100 border border-slate-200 px-3 py-1 rounded-md text-sm text-slate-700'><i class='fa fa-shopping-cart text-slate-400 mr-2'></i> Transação Fechada em Balcão (PDV)</div></div><div class='text-xs text-slate-500 mt-1'>Vínculo Interno: Cupom de Venda #{$tx->source_id}</div>";
                    } else {
                        $origemHtml = "<span class='text-slate-500 italic text-sm'>Lançamento Manual Avulso / Interno</span>";
                    }

                    $autor = optional($tx->actor)->name ?? 'Sistema';
                    $autorHtml = "<strong class='text-slate-800'>{$autor}</strong><br><span class='text-xs text-slate-500'>Operador</span>";

                    $sinal = $tx->type == 'INCOME' ? '+' : '-';
                    $color = $tx->type == 'INCOME' ? 'text-emerald-600' : 'text-rose-600';
                    $valorFmt = "R$ " . number_format($tx->amount_cents / 100, 2, ',', '.');
                    $montanteHtml = "<span class='text-lg font-bold tabular-nums {$color}'>{$sinal} {$valorFmt}</span>";

                    return [
                        'date_id' => $dateIdHtml,
                        'tipo' => $tipoHtml,
                        'origem' => $origemHtml,
                        'autor' => $autorHtml,
                        'montante' => $montanteHtml
                    ];
                }
            )
        );
    }

    /**
     * @OA\Get(
     *     path="/api/finance/transactions/export",
     *     tags={"Gestão Financeira"},
     *     summary="Exportar Balancete Contábil",
     *     description="Gera uma planilha em formato CSV com toda movimentação do sistema ERP.",
     *     @OA\Response(response=200, description="Arquivo CSV")
     * )
     */
    public function exportCsv(Request $request)
    {
        $fileName = 'balancete_financeiro_' . date('Ymd_His') . '.csv';
        $transactions = Transaction::with(['actor'])->orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        ];

        $columns = ['ID_Transacao', 'Data', 'Tipo', 'Categoria', 'Descricao', 'Metodo', 'Valor (R$)', 'Ator/Usuario'];

        $callback = function() use($transactions, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            foreach ($transactions as $tx) {
                $valorStr = number_format($tx->amount_cents / 100, 2, ',', '.');
                $tipoStr = $tx->type === 'INCOME' ? 'Receita' : 'Despesa';
                $ator = $tx->actor ? $tx->actor->name : 'SISTEMA/COFRE';

                $row = [
                    $tx->id,
                    $tx->created_at->format('d/m/Y H:i:s'),
                    $tipoStr,
                    $tx->category ?? 'GERAL',
                    $tx->description ?? '-',
                    $tx->payment_method ?? '-',
                    $valorStr,
                    $ator
                ];
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
