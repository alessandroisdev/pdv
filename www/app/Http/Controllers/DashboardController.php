<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Finance\Models\Transaction;
use App\Modules\Sales\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirecionamento Definitivo ACL Garantido caso acesse diretamente.
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->hasRole('Caixa Operacional') && !$user->hasRole('Super Admin')) {
            return redirect()->route('sales.pos.board');
        }

        $now = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        // 1. Dashboard Metrics
        $totalVendasHoje = Sale::whereBetween('created_at', [$todayStart, $todayEnd])->count();
        $faturamentoHojeCents = Transaction::where('type', 'INCOME')
                                            ->whereBetween('created_at', [$todayStart, $todayEnd])
                                            ->sum('amount_cents');
                                            
        $faturamentoTotalAcumuladoCents = Transaction::where('type', 'INCOME')->sum('amount_cents');

        $ticketMedioCents = $totalVendasHoje > 0 ? (int) ($faturamentoHojeCents / $totalVendasHoje) : 0;

        return view('welcome', compact(
            'totalVendasHoje',
            'faturamentoHojeCents',
            'ticketMedioCents',
            'faturamentoTotalAcumuladoCents'
        ));
    }

    public function transactionsDatatable(Request $request)
    {
        $query = Transaction::select('transactions.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['source_id', 'payment_method'],
                function ($trans) {
                    $typeBag = $trans->type == 'INCOME' 
                        ? "<span style='background: rgba(16, 185, 129, 0.1); color: var(--success); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;'>ENTRADA</span>"
                        : "<span style='background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;'>SAÍDA</span>";

                    $originBag = ($trans->payment_method ?? 'MISTO') . "<br><span style='font-size: 0.75rem; color: var(--text-secondary);'>Recibo: #{$trans->source_id}</span>";

                    $colorClass = $trans->type == 'INCOME' ? 'var(--success)' : 'var(--danger)';
                    $money = number_format($trans->amount_cents / 100, 2, ',', '.');
                    $moneyBag = "<span style='font-weight: 600; color: {$colorClass};'>R$ {$money}</span>";

                    return [
                        'fluxo' => $typeBag,
                        'origem' => $originBag,
                        'montante' => $moneyBag
                    ];
                }
            )
        );
    }

    public function salesDatatable(Request $request)
    {
        $query = Sale::with('items')->select('sales.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                [], // You can't easily search by items through DT globally without join, keep empty or specific
                function ($sale) {
                    $idPad = str_pad($sale->id, 5, '0', STR_PAD_LEFT);
                    $time = $sale->created_at->format('H:i');
                    $cupomBag = "<span style='font-weight: 500;'># {$idPad}</span><br><span style='font-size: 0.75rem; color: var(--text-secondary);'>{$time}</span>";

                    $mix = $sale->items->sum('quantity') . " pçs";
                    $mixBag = "<span style='text-align: center; display: block;'>{$mix}</span>";

                    $money = number_format($sale->total_cents / 100, 2, ',', '.');
                    $moneyBag = "<span style='font-weight: 600; color: var(--text-primary); display: block; text-align: right;'>R$ {$money}</span>";

                    return [
                        'cupom' => $cupomBag,
                        'itens' => $mixBag,
                        'faturado' => $moneyBag
                    ];
                }
            )
        );
    }
}
