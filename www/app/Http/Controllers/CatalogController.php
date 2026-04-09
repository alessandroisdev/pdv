<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Inventory\Models\Product;
use App\Modules\Sales\Models\Sale;
use App\Modules\Sales\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    /**
     * Show the dynamic digital catalog PWA interface.
     */
    public function index()
    {
        // Puxa os produtos ativos (Podemos futuramente agrupar por NCM ou CategoryId)
        // Por hora, MVP mostrará os 50 primeiros Produtos mais vendidos / Ativos
        $products = Product::where('status', 'ACTIVE')
            ->where('stock', '>', 0)
            ->take(50)
            ->get();

        return view('catalog.index', compact('products'));
    }

    /**
     * Receives the AJAX Cart payload from the Omnichannel App
     * and drafts a native ERP Sale.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'table_number' => 'nullable|string|max:50',
            'cart' => 'required|array',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Calculate total and validate stock simultaneously
            $totalCents = 0;
            $itemsToInsert = [];
            
            // Assume Matriz Branch (1) for public catalog MVP
            $branchId = 1;

            foreach ($request->cart as $item) {
                // Lock pessimistic on Product stock
                $product = Product::where('id', $item['id'])
                                  ->where('branch_id', $branchId)
                                  ->lockForUpdate()
                                  ->firstOrFail();

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Produto {$product->name} esgotado. Estoque: {$product->stock}");
                }

                $grossCents = $product->price_cents_sale * $item['qty'];
                $totalCents += $grossCents;

                // Baixar no inventário momentaneamente (ou deixar para quando Caixa liquidar)
                // Vamos deixar a venda PENDENTE, o estoque ainda não abaixa duramente até o Caixa aprovar.

                $itemsToInsert[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price_cents' => $product->price_cents_sale,
                    'total_price_cents' => $grossCents,
                    'ncm_code' => $product->ncm_code,
                    'cfop_code' => $product->cfop_code,
                    'cest_code' => $product->cest_code,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Criar a Venda Pendente
            $sale = Sale::create([
                'branch_id' => $branchId,
                'user_id' => null, // Self-service autoatendimento
                'customer_id' => null, // Poderiamos linkar um CPF futuramente
                'total_cents' => $totalCents,
                'status' => 'PENDING',
                'customer_name' => $request->customer_name ?? 'Autoatendimento',
                'payment_method' => 'PENDING',
            ]);

            // Fill IDs for associative fast bulk insert
            foreach($itemsToInsert as &$row) {
                $row['sale_id'] = $sale->id;
            }

            SaleItem::insert($itemsToInsert);

            // A trigger de Websocket poderia ser chamada aqui para avisar o Backoffice RealTime (Laravel Reverb)
            // event(new \App\Events\NewOmnichannelOrder($sale));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido enviado para a cozinha/caixa!',
                'sale_id' => $sale->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
