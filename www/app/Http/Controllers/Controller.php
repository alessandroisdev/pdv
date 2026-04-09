<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="2.0.0",
 *      title="ERP Premium Enterprise SaaS API",
 *      description="API robusta para o Ecossistema ERP B2B/B2C. \n\n**Novos Módulos Disponíveis:**\n- `Motor Tributário & Fiscal` (Despacho assíncrono)\n- `Estoque & WMS Avançado` (Regras de Curva ABC e Validade)\n- `CRM Oportunidades` (Pipeline Kanban)\n- `Controle Multi-Filial` (Isolamento nativo de Tenant)\n- `Tesouraria DRE` & `Integração Motor Pagamentos (PIX)`\n- `Real-Time Websockets` (Laravel Reverb)",
 *      @OA\Contact(
 *          email="alessandro@gestaopdv.com"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer"
 * )
 */
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
