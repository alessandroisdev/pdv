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
    private function getBoardRoute($request = null)
    {
        $req = $request ?? request();
        return $req->is('terminal*') ? 'terminal.pos.board' : 'sales.pos.board';
    }
    public function index()
    {
        $activeRegister = CashRegister::where('status', 'OPEN')->first();
        if (!$activeRegister) {
            return view('sales::pos.open');
        }
        
        $products = Product::where('status', true)->whereHas('stockMovements')->get();
        // Decorate club price if requested on front
        
        return view('sales::pos.index', compact('activeRegister', 'products'));
    }

    public function checkCustomer(Request $request)
    {
        $doc = preg_replace('/[^0-9]/', '', $request->document);
        $customer = \App\Modules\CRM\Models\Customer::where('document', $doc)->first();
        
        if (!$customer) {
            return response()->json(['found' => false]);
        }
        
        return response()->json([
            'found' => true,
            'name' => $customer->name,
            'is_club' => (bool)$customer->is_club_member
        ]);
    }

    public function registerCustomer(Request $request)
    {
        $val = $request->validate([
            'name' => 'required|string',
            'document' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'lgpd' => 'required|boolean'
        ]);
        
        $doc = preg_replace('/[^0-9]/', '', $val['document']);
        if (\App\Modules\CRM\Models\Customer::where('document', $doc)->exists()) {
            return response()->json(['success' => false, 'message' => 'Documento já cadastrado.'], 400);
        }

        $customer = \App\Modules\CRM\Models\Customer::create([
            'name' => $val['name'],
            'document' => $doc,
            'phone' => $val['phone'],
            'email' => $val['email'],
            'address' => $val['address'],
            'is_club_member' => true, // Auto enters club via POS
            'lgpd_consent' => $val['lgpd']
        ]);

        return response()->json([
            'success' => true,
            'customer' => [
                'name' => $customer->name,
                'is_club' => true
            ]
        ]);
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

            $actor = current_pos_actor();
            if (!$actor) throw new \Exception("Autenticação requerida (Sessão ADM ou PIN Terminal) para autorizar vendas!");
            // 1. Recuperar o Turno Ativo
            $register = CashRegister::where('status', 'OPEN')->first();
            
            if (!$register) {
                throw new \Exception("Nenhum Caixa/Turno aberto. Venda bloqueada.");
            }

            // Setup Customer if provided
            $payloadObj = json_decode($payloadRaw, true);
            $customerDocument = null;
            $customerId = null;
            $isClubMember = false;

            if (!empty($payloadObj['customer_document'])) {
                $docClean = preg_replace('/[^0-9]/', '', $payloadObj['customer_document']);
                $customerDocument = $docClean;
                $crmCustomer = \App\Modules\CRM\Models\Customer::where('document', $docClean)->first();
                if ($crmCustomer) {
                    $customerId = $crmCustomer->id;
                    $isClubMember = $crmCustomer->is_club_member;
                }
            }

            // 2. Fundar a Venda Mestra no Módulo Sales
            $sale = new Sale();
            $sale->cash_register_id = $register->id;
            $sale->seller_id = $actor->id;
            $sale->seller_type = get_class($actor);
            $sale->customer_id = $customerId;
            $sale->customer_document = $customerDocument;
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
                    throw new \Exception("Estoque Insuficiente do produto {$product->name}");
                }

                $appliedPriceCents = $product->price_cents_sale;
                if ($isClubMember && !is_null($product->price_cents_club)) {
                    $appliedPriceCents = $product->price_cents_club;
                }

                $lineTotal = $appliedPriceCents * $item['quantity'];
                $calculatedTotal += $lineTotal;

                // Registrar diminuição no Módulo de Inventário (Em vez de mexer flat amount)
                \App\Modules\Inventory\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'actor_id' => $actor->id,
                    'actor_type' => get_class($actor),
                    'type' => 'OUT',
                    'quantity' => $item['quantity'],
                    'transaction_motive' => 'FRENTE DE CAIXA PDV / VENDA #' . $sale->id
                ]);

                // Gerar Item Impresso (Cupom associado) no Módulo Sales
                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $product->id;
                $saleItem->quantity = $item['quantity'];
                $saleItem->unit_price_cents = $appliedPriceCents;
                $saleItem->save();

                $calculatedTotal += $lineTotal;

            }

            // Trust the calculated total to circumvent DOM injection hacks
            $sale->total_cents = $calculatedTotal;
            $sale->save();

            // 4. Injetar o Polimorfismo Financeiro no Livro Razão (Módulo Financeiro)
            $transaction = new Transaction();
            $transaction->actor_type = get_class($actor);
            $transaction->actor_id = $actor->id;
            $transaction->type = 'INCOME'; // Dinheiro que Entra
            $transaction->amount_cents = $calculatedTotal;
            $transaction->payment_method = strtoupper($paymentMethod);
            
            // Relacionamento Morfado da Transação Apontando para o Recibo Mestre
            $transaction->source_type = Sale::class;
            $transaction->source_id = $sale->id;
            $transaction->save();

            // Salvar e Confirmar DB
            DB::commit();

            return redirect()->route($this->getBoardRoute($request))
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
        
        session(['pos_employee_name' => $employee->name, 'pos_employee_id' => $employee->id]);
        return redirect()->route($this->getBoardRoute($request))->with('success', 'Turno Aberto com Fundo Original!');
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

        return redirect()->route($this->getBoardRoute($request))->with('success', "$type de " . format_money($amount) . " autorizada!");
    }

    public function closeShiftScreen()
    {
        $register = CashRegister::where('status', 'OPEN')->firstOrFail();
        // Não carregamos as vendas para garantir que a tela é "Cega" (Blind)
        return view('sales::pos.close', compact('register'));
    }

    public function closeShift(Request $request)
    {
        $registers = CashRegister::where('status', 'OPEN')->get();
        if ($registers->isEmpty()) abort(404);

        $primaryRegister = $registers->first();
        
        $reportedCents = floatval(str_replace(',', '.', $request->input('reported_amount_cash', 0))) * 100;
        
        // Calcular quanto do sistema de fato era DINHEIRO nas transações. (No MVP, simplificaremos assumindo todas as vendas atreladas ao PDV)
        $systemCashSales = \App\Modules\Finance\Models\Transaction::where('source_type', Sale::class)
            ->whereIn('source_id', $primaryRegister->sales()->pluck('id'))
            ->where('payment_method', 'DINHEIRO')
            ->sum('amount_cents');

        $sangrias = \App\Modules\Sales\Models\CashRegisterMovement::where('cash_register_id', $primaryRegister->id)->where('type', 'SANGRIA')->sum('amount_cents');
        $reforcos = \App\Modules\Sales\Models\CashRegisterMovement::where('cash_register_id', $primaryRegister->id)->where('type', 'REFORCO')->sum('amount_cents');

        $expectedCash = $primaryRegister->initial_cents + $systemCashSales + $reforcos - $sangrias;

        $difference = $reportedCents - $expectedCash;

        foreach ($registers as $reg) {
            $reg->update([
                'status' => 'CLOSED',
                'closed_at' => now(),
                'reported_cents' => ($reg->id === $primaryRegister->id) ? $reportedCents : 0,
                'difference_cents' => ($reg->id === $primaryRegister->id) ? $difference : 0
            ]);
        }

        session()->forget(['pos_employee_name', 'pos_employee_id']);

        return redirect()->route($this->getBoardRoute($request))->with('success', 'Turno Encerrado. Cofre cego protocolado.');
    }
}
