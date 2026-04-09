<?php

namespace App\Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Sales\Models\CashRegister;
use Illuminate\Http\Request;

class CashRegisterController extends Controller
{
    public function index()
    {
        $registers = CashRegister::with('operator')->latest()->paginate(15);
        return view('sales::cash_registers.index', compact('registers'));
    }

    public function show($id)
    {
        $register = CashRegister::with(['operator', 'sales.customer', 'sales.seller'])->findOrFail($id);
        return view('sales::cash_registers.show', compact('register'));
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
