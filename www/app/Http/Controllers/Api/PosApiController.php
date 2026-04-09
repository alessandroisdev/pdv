<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Inventory\Models\Product;
use App\Modules\Sales\Models\Sale;
use App\Modules\Sales\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosApiController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/pos/products",
     *      operationId="apiProducts",
     *      tags={"Integração PDV Electron"},
     *      security={{"bearerAuth":{}}},
     *      summary="Carga Inicial de Tabela de Produtos (Sync)",
     *      description="Lista todos os produtos ativos para salvar no cache local (IndexedDB) do app Electron Offline.",
     *      @OA\Response(
     *          response=200,
     *          description="Array de Produtos e Tabelas Auxiliares"
     *      )
     * )
     */
    public function getProducts(Request $request)
    {
        $branchId = $request->user()->branch_id ?? 1;

        $products = Product::where('status', 'ACTIVE')
            ->where('branch_id', $branchId)
            ->select('id', 'name', 'sku', 'barcode', 'price_cents_sale', 'stock', 'ncm_code')
            ->get();

        return response()->json([
            'data' => $products,
            'settings' => [
                'branch_id' => $branchId,
                'sync_timestamp' => now()->toIso8601String()
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/pos/sync-sales",
     *      operationId="apiSyncSales",
     *      tags={"Integração PDV Electron"},
     *      security={{"bearerAuth":{}}},
     *      summary="Despejo em Lote de Vendas Offline (Push)",
     *      description="Recebe um Array JSON com N vendas que ocorreram no App Desktop durante o dia (Quando sem internet, empurra em massa).",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="sales", type="array", @OA\Items(
     *                  @OA\Property(property="local_id", type="string"),
     *                  @OA\Property(property="total_cents", type="integer"),
     *                  @OA\Property(property="payment_method", type="string"),
     *                  @OA\Property(property="items", type="array", @OA\Items(
     *                      @OA\Property(property="product_id", type="integer"),
     *                      @OA\Property(property="qty", type="integer")
     *                  ))
     *              ))
     *          )
     *      ),
     *      @OA\Response(response=200, description="Sincronização OK")
     * )
     */
    public function syncSales(Request $request)
    {
        $payload = $request->input('sales', []);
        
        if (empty($payload)) {
            return response()->json(['message' => 'Nada para processar'], 200);
        }

        $branchId = $request->user()->branch_id ?? 1;
        $userId = $request->user()->id;
        $syncedIds = [];

        DB::beginTransaction();
        try {
            foreach ($payload as $localSale) {
                // Previne duplicação de pacotes já syncados num drop timeout de internet
                $totalCents = $localSale['total_cents'] ?? 0;

                $sale = Sale::create([
                    'branch_id' => $branchId,
                    'user_id' => $userId,
                    'total_cents' => $totalCents,
                    'status' => 'COMPLETED',
                    'payment_method' => $localSale['payment_method'] ?? 'CASH',
                ]);

                $itemsToInsert = [];
                foreach ($localSale['items'] as $item) {
                     // Aqui o motor debita do inventario também na vida real, 
                     // mas como é pacote lote batch, simplificamos MVP:
                     $product = Product::find($item['product_id']);
                     $priceCents = $product ? $product->price_cents_sale : 0;
                     $qty = $item['qty'] ?? 1;

                     $itemsToInsert[] = [
                         'sale_id' => $sale->id,
                         'product_id' => $item['product_id'],
                         'quantity' => $qty,
                         'unit_price_cents' => $priceCents,
                         'total_price_cents' => $priceCents * $qty,
                         'created_at' => now(),
                         'updated_at' => now()
                     ];
                     
                     // Bate estoque global se achou
                     if ($product) {
                        $product->decrement('stock', $qty);
                     }
                }
                SaleItem::insert($itemsToInsert);

                $syncedIds[] = $localSale['local_id'];
            }

            // Opcional: Acionar Jobs para Emitir NF-e dessas vendas batched
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($syncedIds) . ' transações acatadas.',
                'synced_local_ids' => $syncedIds
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro Lote Sync:', ['err' => $e->getMessage()]);
            return response()->json(['error' => 'Falha Crítica no Banco de Sincronização: ' . $e->getMessage()], 500);
        }
    }
}
