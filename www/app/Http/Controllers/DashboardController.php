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

        // 2. Transações Recentes para preview
        $recentTransactions = Transaction::with(['actor', 'source'])
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();

        // 3. Vendas Recentes
        $recentSales = Sale::with('items.product')
                            ->orderBy('created_at', 'desc')
                            ->take(6)
                            ->get();

        return view('welcome', compact(
            'totalVendasHoje',
            'faturamentoHojeCents',
            'ticketMedioCents',
            'faturamentoTotalAcumuladoCents',
            'recentTransactions',
            'recentSales'
        ));
    }
}
