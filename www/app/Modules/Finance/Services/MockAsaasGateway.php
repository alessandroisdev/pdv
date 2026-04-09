<?php

namespace App\Modules\Finance\Services;

use App\Modules\Finance\Contracts\PaymentGatewayInterface;
use App\Modules\Finance\Models\Installment;
use Illuminate\Support\Str;

class MockAsaasGateway implements PaymentGatewayInterface
{
    public function generatePix(Installment $installment): array
    {
        // Simulação de chamada externa à API do ASAAS 
        // Em um cenário real faríamos: Http::withToken()->post('https://api.asaas.com/v3/payments', [...]);
        
        $gatewayId = 'pay_' . Str::random(12);

        // Simulando a linha mastigada (EMV) do PIX Copia e Cola
        // A especificação real BR Code dita algo como: 00020126...
        $amount = number_format($installment->amount_cents / 100, 2, '.', '');
        $pixPayload = "00020101021226870014br.gov.bcb.pix2565api.asaas.com/v2/pix/qr/asaas-mock-{$gatewayId}5204000053039865405{$amount}5802BR5922ALESSANDRO IS DEV LTDA6008CURITIBA62070503***6304" . strtoupper(Str::random(4));

        return [
            'gateway_id' => $gatewayId,
            'pix_payload' => $pixPayload,
            'gateway_url' => url("/asaas-mock/pix/{$gatewayId}") 
        ];
    }

    public function generateBoleto(Installment $installment): array
    {
        $gatewayId = 'pay_' . Str::random(12);
        
        return [
            'gateway_id' => $gatewayId,
            'pix_payload' => null,
            'gateway_url' => "https://sandbox.asaas.com/b/pdf/{$gatewayId}"
        ];
    }
}
