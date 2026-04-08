<?php

namespace App\Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Finance\Models\Installment;
use App\Modules\Finance\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InstallmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Installment::query();

        // Filtros Básicos
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $installments = $query->orderBy('due_date', 'asc')->paginate(30);

        // Indicadores Base do Painel
        $overduePayablesCount = Installment::where('type', 'PAYABLE')
                                ->where('status', 'PENDING')
                                ->whereDate('due_date', '<', Carbon::today())
                                ->count();
                                
        $overdueReceivablesCount = Installment::where('type', 'RECEIVABLE')
                                ->where('status', 'PENDING')
                                ->whereDate('due_date', '<', Carbon::today())
                                ->count();

        return view('finance::installments.index', compact('installments', 'overduePayablesCount', 'overdueReceivablesCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:PAYABLE,RECEIVABLE',
            'description' => 'required|string|max:255',
            'amount_total' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
        ]);

        $amountCents = (int)($request->input('amount_total') * 100);

        Installment::create([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'amount_cents' => $amountCents,
            'due_date' => $request->input('due_date'),
            'status' => 'PENDING'
        ]);

        return redirect()->route('finance.installments.index')->with('success', 'Título gerado com sucesso na Tesouraria!');
    }

    public function pay(Request $request, Installment $installment)
    {
        if ($installment->status === 'PAID') {
            return redirect()->back()->with('error', 'Esta parcela já consta como paga.');
        }

        // Transação DB Lock para Garantir o Livro Razão
        try {
            DB::beginTransaction();

            $installment->status = 'PAID';
            $installment->paid_date = Carbon::today();
            
            // Gerar o Desconto / Adição no Livro Razão Principal!
            $transaction = new Transaction();
            $transaction->type = $installment->type === 'PAYABLE' ? 'EXPENSE' : 'INCOME';
            $transaction->amount_cents = $installment->amount_cents;
            $transaction->payment_method = 'TRANSFERENCIA'; // Padrão
            
            $actor = Auth::user() ?? \App\Models\User::first();
            $transaction->actor_type = get_class($actor);
            $transaction->actor_id = $actor->id;
            
            $transaction->source_type = Installment::class;
            $transaction->source_id = $installment->id;
            $transaction->save();
            
            $installment->transaction_id = $transaction->id;
            $installment->save();

            DB::commit();
            return redirect()->back()->with('success', 'Baixa executada com sucesso e Livro Razão atualizado!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Falha ao dar baixa: ' . $e->getMessage());
        }
    }
}
