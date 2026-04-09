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
        // Carrega os movimentos ordenados do mais recente ao mais antigo com paginação
        $movements = $product->stockMovements()
            ->with('actor')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('inventory::products.stock', compact('product', 'movements'));
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
