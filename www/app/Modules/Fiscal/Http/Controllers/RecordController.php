<?php

namespace App\Modules\Fiscal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Fiscal\Models\FiscalDocument;
use App\Modules\Fiscal\Services\NfceEngineService;

class RecordController extends Controller
{
    protected $nfeService;

    public function __construct(NfceEngineService $nfeService)
    {
        $this->nfeService = $nfeService;
    }

    public function index()
    {
        return view('fiscal::records.index');
    }

    public function datatable(Request $request)
    {
        $query = FiscalDocument::with('sale')->select('fiscal_documents.*');

        return response()->json(
            \App\Services\DataTableService::process(
                $query, $request,
                ['status', 'message', 'protocol_number'],
                function ($doc) {
                    $idVendaHtml = "<div style='font-weight: bold; color: #334155;'>#{$doc->id}</div>" .
                                   "<div style='font-size: 0.75rem; color: #94a3b8;'>Venda: {$doc->sale_id}</div>";

                    $docType = $doc->document_type ?? 'NFC-E';
                    $modeloHtml = "<span style='display: inline-flex; align-items: center; padding: 0.25rem 0.5rem; border-radius: 0.375rem; font-size: 0.7rem; font-weight: bold; background: #f1f5f9; color: #1e293b;'>{$docType}</span>";

                    $emissaoHtml = "<span class='text-sm text-slate-600'>" . $doc->created_at->format('d/m/Y H:i') . "</span>";

                    // Total vindo do Sale (Venda) atrelada
                    $valor = $doc->sale ? new \App\Modules\Core\ValueObjects\Money($doc->sale->total_cents) : '---';
                    $valorHtml = "<span class='font-mono font-medium text-slate-700' style='font-family: monospace;'>{$valor}</span>";

                    $bg = '#f1f5f9'; $color = '#475569'; $border = '#cbd5e1';
                    if($doc->status === 'AUTORIZADO') { $bg = '#ecfdf5'; $color = '#047857'; $border = '#a7f3d0'; }
                    if($doc->status === 'CONTINGENCIA_OFFLINE') { $bg = '#fffbeb'; $color = '#b45309'; $border = '#fde68a'; }
                    if($doc->status === 'CANCELADO') { $bg = '#fef2f2'; $color = '#b91c1c'; $border = '#fecaca'; }

                    $statusBag = "<span style='display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: bold; background: {$bg}; color: {$color}; border: 1px solid {$border};'>{$doc->status}</span>";
                    
                    if($doc->protocol_number) {
                        $statusBag .= "<div style='font-size: 0.65rem; color: #94a3b8; margin-top: 0.25rem; font-family: monospace;'>Prot: {$doc->protocol_number}</div>";
                    }

                    $acoes = "<div style='display: flex; justify-content: flex-end; gap: 0.5rem;'>";
                    if($doc->status !== 'CANCELADO') {
                        $acoes .= "<button type='button' onclick='confirmCancel({$doc->id})' class='btn text-danger transition-colors' style='background: transparent; border: 1px solid transparent; padding: 0.5rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: bold; cursor: pointer;' title='Cancelar Nota'><i class='fa fa-ban text-danger'></i></button>";
                    }
                    $acoes .= "</div>";
                    $acoes .= "<form id='cancel-form-{$doc->id}' action='".route('fiscal.records.cancel', $doc->id)."' method='POST' style='display: none;'>";
                    $acoes .= "<input type='hidden' name='_token' value='".csrf_token()."'>";
                    $acoes .= "<input type='hidden' name='reason' id='cancel-reason-{$doc->id}'>";
                    $acoes .= "</form>";

                    return [
                        'id_venda' => $idVendaHtml,
                        'modelo'   => $modeloHtml,
                        'emissao'  => $emissaoHtml,
                        'valor'    => $valorHtml,
                        'status'   => $statusBag,
                        'acoes'    => $acoes
                    ];
                }
            )
        );
    }

    public function cancel(Request $request, FiscalDocument $document)
    {
        try {
            // No futuro invocaremos: $this->nfeService->cancelDocument($document, $request->reason);
            // Por enquanto fazemos bypass pro MVP de Mock
            $document->status = 'CANCELADO';
            $document->message = 'Cancelamento Homologado Mock. Motivo: ' . $request->input('reason', 'Erro operacional.');
            $document->save();
            
            return redirect()->back()->with('success', 'Documento Fiscal cancelado com sucesso junto à Sefaz!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao cancelar NF: ' . $e->getMessage());
        }
    }
}
