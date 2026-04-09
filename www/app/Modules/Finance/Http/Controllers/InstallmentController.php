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
        $overduePayablesCount = Installment::where('type', 'PAYABLE')->where('status', 'PENDING')->where('due_date', '<', Carbon::today())->count();
        $overdueReceivablesCount = Installment::where('type', 'RECEIVABLE')->where('status', 'PENDING')->where('due_date', '<', Carbon::today())->count();

        return view('finance::installments.index', compact('overduePayablesCount', 'overdueReceivablesCount'));
    }

    public function datatable(Request $request)
    {
        $query = Installment::select('installments.*');

        // Filtros recebidos pelo GET Parameters da requisicao Ajax
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['description'],
                function ($inst) {
                    $isOverdue = $inst->status === 'PENDING' && $inst->due_date->isPast();

                    $tipoHtml = $inst->type === 'PAYABLE'
                        ? "<span style='display:inline-block; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.75rem; font-weight:bold; background:#ffe4e6; color:#be123c; border:1px solid #fecdd3;'>A PAGAR</span>"
                        : "<span style='display:inline-block; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.75rem; font-weight:bold; background:#d1fae5; color:#047857; border:1px solid #a7f3d0;'>A RECEBER</span>";

                    $descHtml = "<span style='font-weight:500; color:#1e293b;'>{$inst->description}</span>";

                    $valorFmt = "R$ " . number_format($inst->amount_cents / 100, 2, ',', '.');
                    $valorHtml = "<span style='font-family:monospace; font-weight:bold; color:#334155;'>{$valorFmt}</span>";

                    $colorVenc = $isOverdue ? '#e11d48' : '#475569';
                    $boldVenc = $isOverdue ? 'font-weight:bold;' : '';
                    $exclamacao = $isOverdue ? "<i class='fa fa-exclamation-circle' style='margin-left:0.25rem;'></i>" : "";
                    $vencHtml = "<div style='color:{$colorVenc}; {$boldVenc}'>{$inst->due_date->format('d/m/Y')}{$exclamacao}</div>";

                    if ($inst->status === 'PAID') {
                        $pagoEm = $inst->paid_date->format('d/m/y');
                        $statusBadge = "<span style='display:inline-block; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.75rem; font-weight:bold; background:#d1fae5; color:#047857;'>PAGO EM {$pagoEm}</span>";
                    } else {
                        $statusBadge = "<span style='display:inline-block; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.75rem; font-weight:bold; background:#fef3c7; color:#b45309;'>PENDENTE</span>";
                    }
                    
                    $refLivro = $inst->transaction_id ? "<div style='font-size:0.65rem; color:#94a3b8; margin-top:0.25rem;'>Ref Livro Razão: #{$inst->transaction_id}</div>" : "";
                    $statusHtml = $statusBadge . $refLivro;

                    $acoes = "";
                    if ($inst->status === 'PENDING') {
                        $acoes = "<form action='".route('finance.installments.pay', $inst->id)."' method='POST' onsubmit=\"return confirm('Deseja dar a baixa neste título?\\O valor entrará/sairá do Livro Razão (Caixa).')\">"
                            . "<input type='hidden' name='_token' value='".csrf_token()."'>"
                            . "<button type='submit' class='btn btn-outline' style='padding:0.25rem 0.5rem; font-size:0.75rem; font-weight:bold; color:#4f46e5; border-color:#c7d2fe;'>"
                            . "<i class='fa fa-check-circle'></i> Dar Baixa</button></form>";
                    }

                    return [
                        'tipo' => $tipoHtml,
                        'descricao' => $descHtml,
                        'valor' => $valorHtml,
                        'vencimento' => $vencHtml,
                        'status' => $statusHtml,
                        'acoes' => $acoes,
                        'DT_RowClass' => $isOverdue ? 'bg-rose-50' : ''
                    ];
                }
            )
        );
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
