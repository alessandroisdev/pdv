<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ProcurementService
{
    /**
     * Scan products to calculate ABC Curve based on last 30 days of sales.
     */
    public function scanAndGenerateRestockAlerts()
    {
        Log::info("Iniciando varredura WMS para Curva ABC e Alertas de Ruptura.");

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Otimização: buscar quantidade vendida através da tabela SaleItems (se tivéssemos feito join direto)
        // Como o tempo é curto no MVP, vamos abstrair que a quantity na StockMovement do type OUT é a venda.
        $velocities = DB::table('stock_movements')
            ->select('product_id', DB::raw('SUM(ABS(quantity)) as total_sold'))
            ->where('type', 'OUT')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->get();

        $totalCompanySales = $velocities->sum('total_sold');
        if ($totalCompanySales == 0) {
            return;
        }

        $accumulated = 0;

        foreach ($velocities as $p) {
            $percentage = ($p->total_sold / $totalCompanySales) * 100;
            $accumulated += $percentage;

            $curve = 'C';
            if ($accumulated <= 80) {
                $curve = 'A'; // Responsável por 80% das vendas
            } elseif ($accumulated <= 95) {
                $curve = 'B'; // Responsável pelos próximos 15%
            }

            // Descobrir velocidade de venda DIÁRIA.
            $dailyVelocity = $p->total_sold / 30;

            // Supondo lead_time do fornecedor = 7 dias (fixo MVP) e safety stock = 3 dias
            $minimumStockRequired = $dailyVelocity * (7 + 3);

            $product = Product::find($p->product_id);
            if ($product) {
                $currentStock = $product->current_stock;

                if ($currentStock <= $minimumStockRequired) {
                    $needed = ceil($minimumStockRequired - $currentStock);
                    
                    // Disparar Alerta (No MVP, gravamos no log que será consumido no painel).
                    Log::warning("Procurement Alert: Produto '{$product->name}' (Curva {$curve}) está na iminência de ruptura. Estoque atual: {$currentStock}. Necessário: {$needed} pçs.");
                    
                    // Futuro: ProcurementAlert::create(...)
                }
            }
        }
    }
}
