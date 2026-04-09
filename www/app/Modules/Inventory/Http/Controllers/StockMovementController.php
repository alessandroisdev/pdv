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
                ['transaction_motive', 'batch_number'], 
                function ($mov) {
                    $dt = $mov->created_at ? $mov->created_at->format('d/m/Y H:i') : '--';
                    
                    if($mov->type === 'ADJUSTMENT') {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #e0e7ff; color: #4f46e5;'>AJUSTE GERENCIAL</span>";
                    } elseif($mov->type === 'SALE') {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #fef08a; color: #854d0e;'>FRENTE CAIXA PDV</span>";
                    } elseif($mov->type === 'WMS_INBOUND') {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #dcfce7; color: #166534;'>WMS LOGÍSTICA</span>";
                    } else {
                        $type = "<span style='font-size: 0.7rem; font-weight: bold; padding: 3px 6px; border-radius: 4px; background: #e2e8f0; color: #475569;'>{$mov->type}</span>";
                    }

                    $motive = "<span style='font-size: 0.85rem; font-weight: bold; color: #334155;'>{$mov->transaction_motive}</span>";
                    
                    $actor = $mov->actor->name ?? 'Sistema';

                    if ($mov->batch_number) {
                        $expCarbon = \Carbon\Carbon::parse($mov->expires_at);
                        $expFormatted = $expCarbon->format('d/m/Y');
                        
                        $isExpired = $expCarbon->isPast();
                        if ($isExpired) {
                            $badge = "<span style='background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold;'><i class='fa fa-skull'></i> Vencido: {$expFormatted}</span>";
                        } else {
                            $badge = "<span style='background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: bold;'>Validade: {$expFormatted}</span>";
                        }
                        
                        $actorInfo = "
                            <div style='display: flex; flex-direction: column; gap: 0.25rem;'>
                                <span style='font-size: 0.85rem; font-weight: bold; color: #334155;'><i class='fa fa-barcode text-slate-400'></i> Lote: {$mov->batch_number}</span>
                                <div>{$badge}</div>
                                <span style='font-size: 0.7rem; color: #64748b;'>Recebedor: {$actor}</span>
                            </div>
                        ";
                    } else {
                        $actorInfo = "<span style='font-size: 0.85rem; color: #475569;'><i class='fa fa-user-circle'></i> {$actor}</span>";
                    }

                    $qtyStyle = $mov->quantity > 0 ? "background: #d1fae5; color: #047857;" : "background: #ffe4e6; color: #be123c;";
                    $qtySign = $mov->quantity > 0 ? '+' : '';
                    $qty = "<span style='display: inline-block; min-width: 3rem; text-align: center; font-weight: 900; padding: 4px 8px; border-radius: 6px; {$qtyStyle}'>{$qtySign}{$mov->quantity}</span>";

                    return [
                        'm_data' => $dt,
                        'modulo' => $type,
                        'motivo' => $motive,
                        'actor' => $actorInfo,
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

    /**
     * Recebimento Logístico WMS rastreado por Lote e Validade
     */
    public function receiveBatch(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'batch_number' => 'required|string|max:100',
            'expires_at' => 'required|date',
            'transaction_motive' => 'nullable|string|max:255',
        ]);

        $expiresAt = \Carbon\Carbon::parse($request->expires_at);

        // Bloqueio rigoroso de FEFO Logístico: Impedir entrada de lixo/vencido.
        if ($expiresAt->isPast() && !$expiresAt->isToday()) {
            return redirect()->back()
                ->withErrors(['expires_at' => 'Segurança WMS: O lote informado já se encontra vencido! A entrada não pode ser processada.'])
                ->withInput();
        }

        StockMovement::create([
            'product_id' => $product->id,
            'actor_id' => Auth::id() ?? 1,
            'actor_type' => \App\Models\User::class,
            'quantity' => (int) $request->input('quantity'),
            'type' => 'WMS_INBOUND',
            'transaction_motive' => mb_strtoupper($request->input('transaction_motive') ?? 'RECEBIMENTO LOTE FISCAL'),
            'batch_number' => mb_strtoupper($request->input('batch_number')),
            'expires_at' => $expiresAt->toDateString()
        ]);

        return redirect()->route('inventory.products.stock', $product)
                         ->with('success', 'Lote Fiscal Injetado! Rastreabilidade e Validade ativadas neste saldo.');
    }
}
