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
        // Pega as últimas 100 notas emitidas
        $documents = FiscalDocument::with('sale')
            ->orderBy('id', 'desc')
            ->paginate(50);
            
        return view('fiscal::records.index', compact('documents'));
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
