<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Finance\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // Eager load polymorphic identities (Source = The Sale / Purchase associated)
        $transactions = Transaction::with(['actor', 'source'])->latest()->paginate(15);
        
        // Sum total net balance (Income - Expense)
        $income = Transaction::where('type', 'INCOME')->sum('amount_cents');
        $expense = Transaction::where('type', 'EXPENSE')->sum('amount_cents');
        $balanceCents = $income - $expense;

        return view('finance::transactions.index', compact('transactions', 'balanceCents', 'income', 'expense'));
    }
}
