<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Product;
use App\Modules\Sales\Models\CashRegister;
use App\Modules\Sales\Models\Sale;
use App\Modules\Sales\Models\SaleItem;
use App\Modules\Finance\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PointOfSaleController extends Controller
{
    public function index()
    {
        $activeRegister = CashRegister::where('status', 'OPEN')->first();
        
        if (!$activeRegister) {
            return view('sales::pos.open');
        }

        // Apenas produtos com estoque aparecerão nos quadrados rápidos do PDV!
        $products = Product::all()->filter(fn($p) => $p->current_stock > 0);
        return view('sales::pos.index', compact('products', 'activeRegister'));
    }

    public function checkout(Request $request)
    {
        $payloadRaw = $request->input('payload_json');
        
        if (!$payloadRaw) {
            return redirect()->back()->with('error', 'Payload vazio! O Carrinho não foi submetido.');
        }

        // 1. Descriptografar Carrinho
        $payload = json_decode($payloadRaw, true);
        $items = $payload['items'] ?? [];
        $paymentMethod = $payload['payment_method'] ?? 'DINHEIRO';

        if (empty($items)) {
            return redirect()->back()->with('error', 'Venda abortada: Tentativa de transacionar um carrinho vazio.');
        }

        try {
            // == BLOQUEIO MONOLÍTICO DE TRANSAÇÕES ==
            DB::beginTransaction();

            $user = Auth::user();
            
            // 1. Recuperar o Turno Ativo
            $register = CashRegister::where('status', 'OPEN')->first();
            
            if (!$register) {
                throw new \Exception("Nenhum Caixa/Turno aberto. Venda bloqueada.");
            }

            // 2. Fundar a Venda Mestra no Módulo Sales
            $sale = new Sale();
            $sale->cash_register_id = $register->id;
            $sale->seller_id = $user->id; // Aqui seria ideal amarrar o id do Employee do Session, mas usaremos id logado como owner maquina
            $sale->seller_type = get_class($user);
            $sale->total_cents = 0; // Calcularemos sob segurança no backend
            $sale->save();

            $calculatedTotal = 0;

            // 3. Processar Itens individuais e Queimar Estoque
            foreach ($items as $item) {
                // LOCK FOR UPDATE (Pessimistic Locking)
                // Impede que duas Vendas no mesmo exato milissegundo levem o último produto e deixem o Stock negativo!
                /** @var \App\Modules\Inventory\Models\Product $product */
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();
                
                if (!$product) {
                    throw new \Exception("Produto ID {$item['id']} adulterado ou removido pelo Administrador enquanto o carrinho estava aberto.");
                }

                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("Alerta de Quebra de Estoque: '{$product->name}'. Restam apenas {$product->current_stock} pçs disponíveis!");
                }

                // Registrar diminuição no Módulo de Inventário (Em vez de mexer flat amount)
                \App\Modules\Inventory\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'actor_id' => $user->id,
                    'actor_type' => get_class($user),
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'transaction_motive' => 'FRENTE DE CAIXA PDV / VENDA #' . $sale->id
                ]);

                // Gerar Item Impresso (Cupom associado) no Módulo Sales
                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $product->id;
                $saleItem->quantity = $item['quantity'];
                $saleItem->unit_price_cents = $product->sale_price->getCents();
                $saleItem->save();

                $calculatedTotal += ($item['quantity'] * $product->sale_price->getCents());
            }

            // Trust the calculated total to circumvent DOM injection hacks
            $sale->total_cents = $calculatedTotal;
            $sale->save();

            // 4. Injetar o Polimorfismo Financeiro no Livro Razão (Módulo Financeiro)
            $transaction = new Transaction();
            $transaction->actor_type = get_class($user);
            $transaction->actor_id = $user->id;
            $transaction->type = 'INCOME'; // Dinheiro que Entra
            $transaction->amount_cents = $calculatedTotal;
            $transaction->payment_method = strtoupper($paymentMethod);
            
            // Relacionamento Morfado da Transação Apontando para o Recibo Mestre
            $transaction->source_type = Sale::class;
            $transaction->source_id = $sale->id;
            $transaction->save();

            // Salvar e Confirmar DB
            DB::commit();

            return redirect()->route('sales.pos.board')
                   ->with('sale_id', $sale->id)
                   ->with('success', "Baixa de Estoque Realizada. " . format_money($calculatedTotal) . " injetados com sucesso no Caixa!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Falha na Transação POS: ' . $e->getMessage());
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'seller']);
        return view('sales::pos.receipt', compact('sale'));
    }

    public function supervisorOverride(Request $request)
    {
        $pin = $request->input('pin');
        $employee = \App\Modules\AccessControl\Models\Employee::where('pin', $pin)
                                ->where('level', 'SUPERVISOR')
                                ->where('status', true)
                                ->first();
        if ($employee) {
            return response()->json(['success' => true, 'supervisor_name' => $employee->name]);
        }
        return response()->json(['success' => false, 'message' => 'PIN Inválido ou Sem Nível de Supervisor'], 403);
    }

    public function openShift(Request $request) 
    {
        $pin = $request->input('pin');
        $initial = floatval(str_replace(',', '.', $request->input('initial_cash', 0))) * 100;
        
        $employee = \App\Modules\AccessControl\Models\Employee::where('pin', $pin)->first();
        if (!$employee) return redirect()->back()->with('error', 'PIN Numérico Inválido.');

        $register = CashRegister::create([
           'status' => 'OPEN',
           'opened_by_type' => get_class($employee),
           'opened_by_id' => $employee->id,
           'initial_cents' => $initial,
           'opened_at' => now()
        ]);
        
        session(['pos_employee_name' => $employee->name]);
        return redirect()->route('sales.pos.board')->with('success', 'Turno Aberto com Fundo Original!');
    }

    public function cashMovement(Request $request)
    {
        $register = CashRegister::where('status', 'OPEN')->firstOrFail();
        $pin = $request->input('supervisor_pin');
        $amount = floatval(str_replace(',', '.', $request->input('amount'))) * 100;
        $type = $request->input('type'); // SANGRIA ou REFORCO
        $reason = $request->input('reason');

        $supervisor = \App\Modules\AccessControl\Models\Employee::where('pin', $pin)->where('level', 'SUPERVISOR')->first();
        if (!$supervisor) return redirect()->back()->with('error', 'Sangria negada: Falha na identificação do Supervisor.');

        \App\Modules\Sales\Models\CashRegisterMovement::create([
            'cash_register_id' => $register->id,
            'type' => $type,
            'amount_cents' => $amount,
            'reason' => $reason,
            'authorized_by_pin' => $supervisor->name
        ]);

        return redirect()->route('sales.pos.board')->with('success', "$type de " . format_money($amount) . " autorizada!");
    }

    public function closeShiftScreen()
    {
        $register = CashRegister::where('status', 'OPEN')->firstOrFail();
        // Não carregamos as vendas para garantir que a tela é "Cega" (Blind)
        return view('sales::pos.close', compact('register'));
    }

    public function closeShift(Request $request)
    {
        /** @var \App\Modules\Sales\Models\CashRegister $register */
        $register = CashRegister::where('status', 'OPEN')->firstOrFail();

        
        $reportedCents = floatval(str_replace(',', '.', $request->input('reported_amount_cash', 0))) * 100;
        
        // Calcular quanto do sistema de fato era DINHEIRO nas transações. (No MVP, simplificaremos assumindo todas as vendas atreladas ao PDV)
        $systemCashSales = \App\Modules\Finance\Models\Transaction::where('source_type', Sale::class)
            ->whereIn('source_id', $register->sales()->pluck('id'))
            ->where('payment_method', 'DINHEIRO')
            ->sum('amount_cents');

        $sangrias = \App\Modules\Sales\Models\CashRegisterMovement::where('cash_register_id', $register->id)->where('type', 'SANGRIA')->sum('amount_cents');
        $reforcos = \App\Modules\Sales\Models\CashRegisterMovement::where('cash_register_id', $register->id)->where('type', 'REFORCO')->sum('amount_cents');

        $expectedCash = $register->initial_cents + $systemCashSales + $reforcos - $sangrias;

        $difference = $reportedCents - $expectedCash;

        $register->update([
            'status' => 'CLOSED',
            'closed_at' => now(),
            'reported_cents' => $reportedCents,
            'difference_cents' => $difference
        ]);

        session()->forget('pos_employee_name');

        return redirect()->route('sales.pos.board')->with('success', 'Turno Encerrado. Cofre cego protocolado.');
    }
}
