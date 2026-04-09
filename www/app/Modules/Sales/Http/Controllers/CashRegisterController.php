<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sales\Models\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function index()
    {
        return view('sales::cash_registers.index');
    }

    public function datatable(Request $request)
    {
        $query = CashRegister::with('operator')->select('cash_registers.*');
        
        // Filtro Status Customizado
        if ($request->filled('status')) {
            if ($request->status === 'open') {
                $query->whereNull('closed_at');
            } elseif ($request->status === 'closed') {
                $query->whereNotNull('closed_at');
            }
        }

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                [], // Não há campos de texto bons em quebra de caixa pra string pura a menos que seja relacionamentos
                function ($register) {
                    $isOpen = is_null($register->closed_at);
                    
                    $turnoHtml = "#" . str_pad($register->id, 5, '0', STR_PAD_LEFT);
                    $operadorHtml = "<span class='fw-semibold'>" . ($register->operator->name ?? 'PDV User') . "</span>";
                    
                    $statusBg = $isOpen ? 'background: #dcfce7; color: #166534;' : 'background: #e2e8f0; color: #475569;';
                    $statusText = $isOpen ? 'ABERTO' : 'FECHADO';
                    $statusHtml = "<span style='display:inline-block; padding:0.2rem 0.6rem; border-radius:4px; font-size:0.75rem; font-weight:600; {$statusBg}'>{$statusText}</span>";
                    
                    $aberturaHtml = $register->opened_at ? $register->opened_at->format('d/m/Y H:i') : '';
                    $fundoTrocoHtml = "<span class='fw-semibold text-emerald-600'>R$ " . number_format($register->initial_cents / 100, 2, ',', '.') . "</span>";
                    $fechamentoHtml = "<span class='text-light'>" . ($register->closed_at ? $register->closed_at->format('d/m/Y H:i') : '---') . "</span>";
                    
                    $btnRoute = route('sales.cash_registers.show', $register->id);
                    $opcoesHtml = "<a href='{$btnRoute}' class='btn' style='padding:0.25rem 0.5rem; border:1px solid #e2e8f0; color:#455073; font-size:0.75rem; text-decoration:none; display:inline-block;'>Auditar</a>";

                    return [
                        'turno' => $turnoHtml,
                        'operador' => $operadorHtml,
                        'status' => $statusHtml,
                        'abertura' => $aberturaHtml,
                        'fundo' => $fundoTrocoHtml,
                        'fechamento' => $fechamentoHtml,
                        'opcoes' => $opcoesHtml,
                    ];
                }
            )
        );
    }

    public function show($id)
    {
        // Nós removemos a query gigantesca "sales.customer, sales.seller" porque agora as sales usarão Ajax.
        // Mas mantemos a agregada para os top cards.
        $register = CashRegister::with(['operator'])->findOrFail($id);
        
        // Calculamos os subtotais aqui para nao depender carregar todas as collections
        $totalSalesCents = \App\Modules\Sales\Models\Sale::where('cash_register_id', $id)->sum('total_cents');
        $totalSalesCount = \App\Modules\Sales\Models\Sale::where('cash_register_id', $id)->count();

        return view('sales::cash_registers.show', compact('register', 'totalSalesCents', 'totalSalesCount'));
    }

    public function salesDatatable(Request $request, $id)
    {
        $query = \App\Modules\Sales\Models\Sale::with(['customer', 'seller'])
            ->where('cash_register_id', $id)
            ->select('sales.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['customer_document'], // Simple fields for like search...
                function ($sale) {
                    $cupomHtml = "<strong style='color:#1e293b;'>#" . str_pad($sale->id, 6, '0', STR_PAD_LEFT) . "</strong>";

                    $clienteHtml =  ($sale->customer->name ?? 'CONSUMIDOR (SEM NOME)') . "
                                    <div style='font-size:0.75rem; color:#94a3b8; font-family:monospace; margin-top:2px;'>" . 
                                    ($sale->customer_document ?? 'Sem CPF') . "
                                    </div>";

                    $operadorHtml = $sale->seller->name ?? 'Usuário Desconhecido';
                    
                    $horaHtml = $sale->created_at ? $sale->created_at->format('H:i:s') : '--';
                    
                    $moneyStr = number_format($sale->total_cents / 100, 2, ',', '.');
                    $totalHtml = "<span style='font-weight:900; color:#059669; font-size:1.1rem; letter-spacing:-0.5px;'>{$moneyStr}</span>";

                    return [
                        'cupom' => $cupomHtml,
                        'cliente' => $clienteHtml,
                        'operador' => $operadorHtml,
                        'hora' => $horaHtml,
                        'total' => $totalHtml
                    ];
                }
            )
        );
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'relatorio_fechamentos_' . date('Ymd_His') . '.csv';
        $registers = CashRegister::with(['operator'])->orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        ];

        $columns = ['Turno_ID', 'Operador', 'Status', 'Data_Abertura', 'Fundo_Inicial_R$', 'Data_Fechamento', 'Quebra_Diferenca_R$'];

        $callback = function() use($registers, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            foreach ($registers as $reg) {
                $status = is_null($reg->closed_at) ? 'ABERTO' : 'FECHADO';
                $operador = $reg->operator ? $reg->operator->name : 'PDV User';
                $fundoDiv = number_format($reg->initial_cents / 100, 2, ',', '.');
                $diferenca = number_format(($reg->difference_cents ?? 0) / 100, 2, ',', '.');

                $row = [
                    $reg->id,
                    $operador,
                    $status,
                    $reg->opened_at ? $reg->opened_at->format('d/m/Y H:i:s') : '---',
                    $fundoDiv,
                    $reg->closed_at ? $reg->closed_at->format('d/m/Y H:i:s') : '---',
                    $diferenca
                ];
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
