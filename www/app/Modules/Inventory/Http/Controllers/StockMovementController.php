<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Product;
use App\Modules\Inventory\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    /**
     * Exibe o histórico de movimentações (Livro-Razão) e o formulário de ajuste.
     */
    public function index(Product $product)
    {
        return view('inventory::products.stock', compact('product'));
    }

    public function datatable(Request $request, Product $product)
    {
        $query = \App\Modules\Inventory\Models\StockMovement::with('actor')
            ->where('product_id', $product->id)
            ->select('stock_movements.*');
        
        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['transaction_motive'], 
                function ($mov) {
                    $dt = $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '--';
                    
                    if($mov->type === 'ADJUSTMENT') {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #e0e7ff; color: #4f46e5;'>AJUSTE GERENCIAL</span>";
                    } elseif($mov->type === 'SALE') {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #fef08a; color: #854d0e;'>FRENTE CAIXA PDV</span>";
                    } else {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #e2e8f0; color: #475569;'>{$mov->type}</span>";
                    }

                    $motive = "<span style='font-size: 0.85rem; font-weight: bold; color: #334155;'>{$mov->transaction_motive}</span>";
                    
                    $actor = $mov->actor->name ?? 'Sistema';

                    $qtyStyle = $mov->quantity > 0 ? "background: #d1fae5; color: #047857;" : "background: #ffe4e6; color: #be123c;";
                    $qtySign = $mov->quantity > 0 ? '+' : '';
                    $qty = "<span style='display: inline-block; min-width: 3rem; text-align: center; font-weight: 900; padding: 4px 8px; border-radius: 6px; {$qtyStyle}'>{$qtySign}{$mov->quantity}</span>";

                    return [
                        'm_data' => $dt,
                        'modulo' => $type,
                        'motivo' => $motive,
                        'actor' => $actor,
                        'mutacao' => $qty
                    ];
                }
            )
        );
    }

    /**
     * Processa um Ajuste Manual (Balanço Físico).
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'operation' => 'required|in:in,out',
            'quantity' => 'required|integer|min:1',
            'transaction_motive' => 'required|string|max:255',
        ]);

        $quantity = (int) $request->input('quantity');
        $operation = $request->input('operation');
        
        // Se for saída, transformar em incremento negativo
        if ($operation === 'out') {
            $quantity = -$quantity;
        }

        // Criar o registro imutável
        StockMovement::create([
            'product_id' => $product->id,
            'actor_id' => Auth::id() ?? 1, // Fallback p/ admin se nulo por acidente na sessão
            'actor_type' => \App\Models\User::class, // Assinando como Usuário Polimórfico
            'quantity' => $quantity,
            'type' => 'ADJUSTMENT',
            'transaction_motive' => mb_strtoupper($request->input('transaction_motive')),
        ]);

        return redirect()->route('inventory.products.stock', $product)
                         ->with('success', 'Ajuste de estoque contabilizado com sucesso!');
    }
}
