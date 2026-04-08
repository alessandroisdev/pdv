<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Inventory\Models\Product;
use App\Modules\Sales\Models\CashRegister;
use App\Modules\Sales\Models\Sale;
use App\Modules\Sales\Models\SaleItem;
use App\Modules\Finance\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    /**
     * Exibe o Catálogo Público (Acessível via QRCode pela Mesa / Cliente Front)
     */
    public function index()
    {
        // Pega produtos ativos e filtra na memória via a soma do Módulo de Inventário
        $products = Product::where('status', 'ACTIVE')
                           ->orderBy('name', 'asc')
                           ->get()
                           ->filter(function($product) {
                               return $product->current_stock > 0;
                           });

        return view('sales::catalog.index', compact('products'));
    }

    /**
     * Processa o Checkout nativo do Catálogo
     */
    public function checkout(Request $request)
    {
        $payload = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:PIX,CREDIT_CARD,DEBIT_CARD,MONEY',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Totem Self-Service requer um Caixa Aberto Matriz
            $register = CashRegister::where('status', 'OPEN')->first();
            if (!$register) {
                return response()->json(['success' => false, 'message' => 'Desculpe! A loja se encontra fechada no momento.'], 400);
            }

            // Iniciar venda
            $sale = new Sale();
            $sale->cash_register_id = $register->id;
            $sale->status = 'COMPLETED'; // Venda online já nasce completada
            $sale->total_cents = 0;
            // Para efeitos de documentação, guardaremos o nome no documento (ou no future campo nome)
            $sale->customer_document = $payload['customer_name'] ?? 'Cliente Self-Service';
            $sale->save();

            $total = 0;
            foreach ($payload['items'] as $itemData) {
                // LOCK FOR UPDATE: Se 2 mesas tentarem pedir a última Coca, a 2ª vai falhar elegantemente
                $product = Product::lockForUpdate()->find($itemData['product_id']);
                
                if ($product->current_stock < $itemData['quantity']) {
                    throw new \Exception("Vix! Alguém foi mais rápido. O estoque de {$product->name} esgotou.");
                }

                \App\Modules\Inventory\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'actor_id' => 1, // Robô
                    'actor_type' => 'App\Models\User',
                    'type' => 'OUT',
                    'quantity' => $itemData['quantity'],
                    'transaction_motive' => 'FRENTE DE CAIXA TOTEM / VENDA #' . $sale->id
                ]);

                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $product->id;
                $saleItem->quantity = $itemData['quantity'];
                $saleItem->unit_price_cents = $product->price_cents_sale;
                $saleItem->save();

                $total += ($product->price_cents_sale * $itemData['quantity']);
            }

            $sale->total_cents = $total;
            $sale->save();

            // Registra no Livro Razão
            $transaction = new Transaction();
            $transaction->type = 'INCOME';
            $transaction->amount_cents = $total;
            $transaction->payment_method = $payload['payment_method'];
            $transaction->actor_type = 'App\Models\User'; 
            $transaction->actor_id = 1; // Robô de Integração padrão
            $transaction->source_type = Sale::class;
            $transaction->source_id = $sale->id;
            $transaction->save();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'Seu pedido foi processado com sucesso! Aguarde ser chamado.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
