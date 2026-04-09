<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Modules\Sales\Models\Sale;
use App\Modules\Fiscal\Models\FiscalDocument;
use Illuminate\Support\Facades\Log;

class ProcessFiscalDocumentDispatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $sale;

    /**
     * Número de vezes que o job pode tentar executar antes de falhar de vez.
     */
    public $tries = 3;

    /**
     * Tempo a aguardar antes de retentar (Exponential Backoff simulado se configurado em Horizon).
     */
    public $backoff = [10, 30, 60]; 

    /**
     * Create a new job instance.
     */
    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("Processando emissão de documento fiscal (NFC-e) em Background para Venda #" . $this->sale->id);

        try {
            // Simulando um serviço pesado de Sefaz (Assinatura XML, Envio, Espera Recibo).
            // No mundo real: $nfeService->transmit($this->sale);
            
            // Simulação (Sleep artificial de 2 segundos para fins didáticos de processamento de fila)
            sleep(2);

            // Cria o registro no BD
            $doc = FiscalDocument::create([
                'sale_id' => $this->sale->id,
                'document_type' => 'NFC-E',
                'status' => 'AUTORIZADO',
                'protocol_number' => '141' . rand(100000000000, 999999999999), // Ex: 141...
                'xml_path' => null,
                'message' => 'Autorizado o uso da NFC-e'
            ]);

            Log::info("NFC-e Autorizada com Sucesso! Protocolo: " . $doc->protocol_number);

        } catch (\Exception $e) {
            Log::error("Erro ao emitir NFC-e em Background: " . $e->getMessage());
            
            // Caso falhe, se tiver mais tentativas, será jogado novamente na fila e capturado pelo try-catch.
            // Aqui poderíamos marcar o documento como rejeitado caso não haja mais tentativas.
            throw $e;
        }
    }
}
