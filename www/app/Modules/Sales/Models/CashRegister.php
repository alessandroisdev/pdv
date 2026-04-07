<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'status',          // OPEN, CLOSED
        'opened_by_id',    // ID do atendente logado via Auth ou PIN numérico
        'opened_by_type',  // App\Models\User ou App\Modules\AccessControl\Models\Employee
        'initial_cents',   // Fundo de Troco que o dia começou
        'opened_at',
        'reported_cents',
        'difference_cents',
        'closed_at'
    ];

    public function operator()
    {
        return $this->morphTo('opened_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
