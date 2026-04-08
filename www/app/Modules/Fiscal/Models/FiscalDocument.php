<?php

namespace App\Modules\Fiscal\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Sales\Models\Sale;

class FiscalDocument extends Model
{
    protected $fillable = [
        'sale_id', 'model', 'series', 'number',
        'xml_signed', 'xml_authorized', 'xml_canceled',
        'status', 'access_key', 'receipt_protocol', // PROTOCOLO DE AUTORIZAÇÃO
        'message', 'type', // type: NORMAL, CONTINGENCIA
        'environment' // 1: Produção, 2: Homologação
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
