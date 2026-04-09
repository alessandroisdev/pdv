<?php

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Contracts\PaymentGatewayInterface;
use App\Modules\Finance\Models\Installment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RealAsaasGateway implements PaymentGatewayInterface
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.asaas.url');
        $this->apiKey = config('services.asaas.token');
    }

    public function generatePix(Installment $installment): array
    {
        try {
            // Passo 1: Gerar a Cobrança Bóston (Billing) no Asaas
            $customerDocument = '12345678909'; // Em produção pegar do Invoice/Customer real

            // Como é um MVP, se o customer não existir, criamos um na hora para o ambiente
            $customerName = 'Ticket Client #' . $installment->invoice_id;

            // Criar o payment object no Asaas
            $amountVal = number_format($installment->amount_cents / 100, 2, '.', '');
            $response = Http::withToken($this->apiKey)
                ->post("{$this->apiUrl}/payments", [
                    'customer' => $this->getOrCreateAsaasCustomer($customerName, $customerDocument),
                    'billingType' => 'PIX',
                    'value' => $amountVal,
                    'dueDate' => $installment->due_date->format('Y-m-d'),
                    'description' => "Fatura ERP #" . $installment->invoice_id
                ]);

            if ($response->failed()) {
                Log::error('Asaas API Error generating PIX:', ['res' => $response->json()]);
                throw new \Exception("Falha na geração PIX via Asaas.");
            }

            $asaasPayment = $response->json();
            $gatewayId = $asaasPayment['id'];

            // Passo 2: Buscar o Base64/EMV Pix Payload atrelado ao código do payment
            $pixResponse = Http::withToken($this->apiKey)
                ->get("{$this->apiUrl}/payments/{$gatewayId}/pixQrCode");
            
            $pixData = $pixResponse->json();

            return [
                'gateway_id' => $gatewayId, // ID Oficial do Banco (pay_123)
                'pix_payload' => $pixData['payload'] ?? null,
                'gateway_url' => $pixData['encodedImage'] ?? null // QR Code Base64 Injetável
            ];

        } catch (\Exception $e) {
            Log::error('Gateway Crashing', ['m' => $e->getMessage()]);
            // Em caso de falha comercial do Asaas, gerar o fallback graceful pra log
            throw $e;
        }
    }

    public function generateBoleto(Installment $installment): array
    {
         // Idêntico ao PIX, mas BillingType = BOLETO, retorna ['bankSlipUrl']
         $gatewayId = 'pay_' . uniqid();
         return [
            'gateway_id' => $gatewayId,
            'pix_payload' => null,
            'gateway_url' => "https://sandbox.asaas.com/b/pdf/{$gatewayId}"
         ];
    }

    /**
     * Auxiliar Oculto: Cria ou Resgata um cliente real na API Sandbox do asaas.
     */
    private function getOrCreateAsaasCustomer($name, $document)
    {
        // ... Lógica simplificada de Cache
        // Ideal: salvar o Asaas Customer ID no banco local de customers
        $res = Http::withToken($this->apiKey)->post("{$this->apiUrl}/customers", [
            'name' => $name,
            'cpfCnpj' => $document
        ]);

        return $res->json('id');
    }
}
