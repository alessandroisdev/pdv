<?php

namespace App\Modules\Finance\Contracts;

use App\Modules\Finance\Models\Installment;

interface PaymentGatewayInterface
{
    /**
     * Gera uma transação PIX Dinâmica no gateway escolhido.
     * Deve retornar um array contendo os dados do payload e/ou qrcode_url.
     * 
     * @return array [ 'gateway_id', 'pix_payload', 'gateway_url' ]
     */
    public function generatePix(Installment $installment): array;

    /**
     * Gera um Boleto Bancário no gateway.
     */
    public function generateBoleto(Installment $installment): array;
}
