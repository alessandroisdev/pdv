<?php

namespace App\Modules\Sales\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Sales\Models\Sale;
use App\Modules\Sales\Models\SaleItem;
use App\Modules\Sales\Models\CashRegister;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/omnichannel/checkout",
     *     tags={"Omnichannel & Delivery"},
     *     summary="Processa Venda via App Externo/Delivery",
     *     description="Recebe o array de produtos de um App Mobile React Native e processa direto na Frente de Caixa, exigindo integração transparente.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="customer_document", type="string", description="CPF do Consumidor"),
     *             @OA\Property(property="payment_method", type="string", enum={"PIX", "CREDIT_CARD"}),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="product_id", type="integer"),
     *                     @OA\Property(property="quantity", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Venda Processada com Sucesso pela Matriz"
     *     )
     * )
     */
    public function processDeliverySale(Request $request)
    {
        $payload = $request->validate([
            'customer_document' => 'nullable|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            $register = CashRegister::where('status', 'OPEN')->first();
            if (!$register) {
                return response()->json(['error' => 'Nenhum Caixa Aberto para Atrelar a Venda da Internet'], 400);
            }

            // A lógica espelha o PointOfSaleController, mas para APIs!
            $sale = new Sale();
            $sale->cash_register_id = $register->id;
            $sale->status = 'COMPLETED';
            $sale->total_cents = 0;
            $sale->customer_document = $payload['customer_document'] ?? null;
            $sale->save();

            $total = 0;
            foreach ($payload['items'] as $itemData) {
                $product = \App\Modules\Inventory\Models\Product::lockForUpdate()->find($itemData['product_id']);
                
                if ($product->stock_quantity < $itemData['quantity']) {
                    throw new \Exception("Estoque insuficiente para o produto {$product->name}");
                }

                $product->stock_quantity -= $itemData['quantity'];
                $product->save();

                $saleItem = new SaleItem();
                $saleItem->sale_id = $sale->id;
                $saleItem->product_id = $product->id;
                $saleItem->quantity = $itemData['quantity'];
                $saleItem->unit_price_cents = $product->price_cents;
                $saleItem->save();

                $total += ($product->price_cents * $itemData['quantity']);
            }

            $sale->total_cents = $total;
            $sale->save();

            // Lança a transação Financeira
            $transaction = new \App\Modules\Finance\Models\Transaction();
            $transaction->type = 'INCOME';
            $transaction->amount_cents = $total;
            $transaction->payment_method = $payload['payment_method'];
            $transaction->actor_type = 'App\Models\User';
            $transaction->actor_id = 1; // Padrão Robo da API
            $transaction->save();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'message' => 'Delivery sincronizado com sucesso e estoque deduzido!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
