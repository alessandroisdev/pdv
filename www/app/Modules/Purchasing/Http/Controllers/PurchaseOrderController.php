<?php

namespace App\Modules\Purchasing\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\PurchaseOrderItem;
use App\Modules\Purchasing\Models\Supplier;
use App\Modules\Inventory\Models\Product;
use App\Modules\Finance\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchasing::orders.index');
    }

    public function datatable(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items'])->select('purchase_orders.*');
        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['invoice_number'], // Supplier Name search would require join, so limit text search to invoice_number
                function ($order) {
                    $pedidoHtml = "<strong style='color:#1e293b;'>#" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . "</strong>
                                   <div class='text-light mt-1' style='font-size:0.75rem;'>NF: " . ($order->invoice_number ?? 'Sem Nota') . "</div>";

                    $fornecedorHtml = "<span style='font-weight:600; color:#455073;'>" . ($order->supplier->company_name ?? 'Desconhecido') . "</span>";

                    if ($order->status === 'RECEIVED') {
                        $dtAttr = $order->received_at ? $order->received_at->format('d/m/y H:i') : '';
                        $statusHtml = "<div style='text-align:center;'><span style='background-color:#d1fae5; color:#047857; font-size:0.75rem; font-weight:bold; padding:2px 6px; border-radius:4px;'>RECEBIDO</span>
                                       <div style='font-size:0.75rem; color:#059669; margin-top:4px;' title='Entrou no estoque'>{$dtAttr}</div></div>";
                    } elseif ($order->status === 'PENDING') {
                        $statusHtml = "<div style='text-align:center;'><span style='background-color:#fef3c7; color:#92400e; font-size:0.75rem; font-weight:bold; padding:2px 6px; border-radius:4px;'>RASCUNHO / EM TRÂNSITO</span></div>";
                    } else {
                        $statusHtml = "<div style='text-align:center;'><span style='background-color:#f1f5f9; color:#1e293b; font-size:0.75rem; font-weight:bold; padding:2px 6px; border-radius:4px;'>{$order->status}</span></div>";
                    }

                    $qtdHtml = "<div style='text-align:center; color:#64748b;'>" . $order->items->count() . " Lote(s)</div>";
                    
                    $money = new \App\Modules\Core\ValueObjects\Money($order->total_cents);
                    $totalHtml = "<div style='font-weight:bold; color:#1e293b;'>{$money}</div>";

                    if ($order->status === 'PENDING') {
                        $btnUrl = route('purchasing.orders.receive', $order->id);
                        $csrf = csrf_token();
                        $acaoHtml = "<form action='{$btnUrl}' method='POST' id='receive-form-{$order->id}'>
                                        <input type='hidden' name='_token' value='{$csrf}'>
                                        <button type='button' onclick='confirmReceive({$order->id})' class='btn' style='background-color:#059669; color:white; padding:0.25rem 0.75rem; font-size:0.875rem; border:none;'>
                                            <i class='fa fa-arrow-down' style='font-size:10px;'></i> Entrada
                                        </button>
                                     </form>";
                    } else {
                        $acaoHtml = "<button disabled class='btn btn-outline' style='padding: 0.25rem 0.75rem; font-size: 0.875rem; opacity: 0.5;' title='Apenas visualização em auditoria futura'>Fechado</button>";
                    }

                    return [
                        'pedido' => $pedidoHtml,
                        'fornecedor' => $fornecedorHtml,
                        'status' => $statusHtml,
                        'qtd' => $qtdHtml,
                        'total' => $totalHtml,
                        'acoes' => $acaoHtml
                    ];
                }
            )
        );
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        return view('purchasing::orders.create', compact('suppliers'));
    }

    public function searchProduct(Request $request)
    {
        $q = $request->query('q');
        
        if (empty($q)) {
            return response()->json([]);
        }

        $products = Product::where('barcode', $q)
                           ->orWhere('sku', $q)
                           ->orWhere('name', 'LIKE', '%' . $q . '%')
                           ->limit(15)
                           ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|string'
        ]);

        $itemsRaw = json_decode($request->input('items'), true);
        if (empty($itemsRaw)) {
            return redirect()->back()->with('error', 'O carrinho de compras não pode estar vazio.');
        }

        DB::beginTransaction();
        try {
            $totalCents = 0;
            foreach ($itemsRaw as $i) {
                // Ensure number mapping safely
                $qty = (int) $i['quantity'];
                $price = (int) $i['unit_price_cents'];
                $totalCents += ($qty * $price);
            }

            $order = PurchaseOrder::create([
                'supplier_id' => $request->input('supplier_id'),
                'user_id' => Auth::id() ?? 1,
                'status' => 'PENDING',
                'invoice_number' => $request->input('invoice_number'),
                'total_cents' => $totalCents,
                'notes' => $request->input('notes')
            ]);

            foreach ($itemsRaw as $i) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id' => $i['id'],
                    'quantity' => (int) $i['quantity'],
                    'unit_price_cents' => (int) $i['unit_price_cents'],
                    'total_cents' => ((int) $i['quantity'] * (int) $i['unit_price_cents'])
                ]);
            }

            DB::commit();
            return redirect()->route('purchasing.orders.index')->with('success', 'Pedido (Rascunho) arquivado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro interno ao salvar pedido: ' . $e->getMessage());
        }
    }

    public function receive(PurchaseOrder $order)
    {
        if ($order->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Apenas pedidos status PENDENTE podem ser ingressados.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'RECEIVED',
                'received_at' => now(),
                'user_id' => Auth::id() ?? 1
            ]);

            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product) {
                    $product->price_cents_cost = $item->unit_price_cents;
                    $product->save();

                    \App\Modules\Inventory\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'actor_id' => Auth::id() ?? 1,
                        'actor_type' => User::class,
                        'type' => 'IN',
                        'quantity' => $item->quantity,
                        'transaction_motive' => 'NF-E RECEBIMENTO / COMPRA'
                    ]);
                }
            }

            Transaction::create([
                'actor_id' => Auth::id() ?? 1,
                'actor_type' => User::class,
                'type' => 'EXPENSE',
                'amount_cents' => $order->total_cents,
                'payment_method' => 'BOLETO', // Fixo provisório para notas de fornecedor
                'source_id' => $order->id,
                'source_type' => PurchaseOrder::class
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Nota Fiscal Integrada! Estoque (' . $order->items->sum('quantity') . ' volumes) e Despesa lançados com precisão auditável.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Falha sistêmica no cruzamento transacional: ' . $e->getMessage());
        }
    }
}
