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
        // Apenas produtos com estoque aparecerão nos quadrados rápidos do PDV!
        $products = Product::where('stock', '>', 0)->get();
        return view('sales::pos.index', compact('products'));
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

            // 1. Obter ou Criar um Caixa/Turno aberto para a sessão atual
            $register = CashRegister::firstOrCreate(
                [
                    'status' => 'OPEN', 
                    'opened_by_id' => $user->id, 
                    'opened_by_type' => get_class($user)
                ],
                [
                    'initial_cents' => 0,
                    'opened_at' => now()
                ]
            );

            // 2. Fundar a Venda Mestra no Módulo Sales
            $sale = new Sale();
            $sale->cash_register_id = $register->id;
            $sale->seller_id = $user->id;
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

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Alerta de Quebra de Estoque: '{$product->name}'. Restam apenas {$product->stock} pçs disponíveis!");
                }

                // Decrementar do Módulo Inventário
                $product->stock -= $item['quantity'];
                $product->save();

                // Gerar Item Impresso (Cupom associado) no Módulo Sales
                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $product->id;
                $saleItem->quantity = $item['quantity'];
                $saleItem->unit_price_cents = $product->price->getCents();
                $saleItem->save();

                $calculatedTotal += ($item['quantity'] * $product->price->getCents());
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

            return redirect()->route('sales.pos.board')->with('success', "Baixa de Estoque Realizada. R\$ " . number_format($calculatedTotal/100, 2, ',', '.') . " injetados com sucesso no Caixa!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Falha na Transação POS: ' . $e->getMessage());
        }
    }
}
