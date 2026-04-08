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
        // Eager load polymorphic identities
        $transactions = Transaction::with(['actor', 'source'])->latest()->paginate(20);
        
        // Sum total net balance
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

        return view('finance::transactions.index', compact('transactions', 'balanceCents', 'income', 'expense', 'todayIncome', 'ticketMedio', 'caixasComDivergencia'));
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
