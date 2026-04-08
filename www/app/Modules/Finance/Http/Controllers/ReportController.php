<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Finance\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Estatísticas Básicas de Hoje
        $todayIncome = Transaction::where('type', 'INCOME')->whereDate('created_at', Carbon::today())->sum('amount_cents');
        $todayExpense = Transaction::where('type', 'EXPENSE')->whereDate('created_at', Carbon::today())->sum('amount_cents');
        $profitMargin = $todayIncome > 0 ? (($todayIncome - $todayExpense) / $todayIncome) * 100 : 0;

        // Gráfico de 7 Dias Anteriores
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $inc = Transaction::where('type', 'INCOME')->whereDate('created_at', $date)->sum('amount_cents');
            $exp = Transaction::where('type', 'EXPENSE')->whereDate('created_at', $date)->sum('amount_cents');
            $last7Days->push([
                'date' => $date->format('d/m'),
                'income' => $inc / 100, // Pass in normal float for Chart JS
                'expense' => $exp / 100
            ]);
        }
        
        $chartData = [
            'labels' => $last7Days->pluck('date')->toArray(),
            'income' => $last7Days->pluck('income')->toArray(),
            'expense' => $last7Days->pluck('expense')->toArray()
        ];

        return view('finance::reports.index', compact('todayIncome', 'todayExpense', 'profitMargin', 'chartData'));
    }
}
