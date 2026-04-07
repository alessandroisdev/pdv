<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegisterMovement extends Model
{
    protected $fillable = [
        'cash_register_id',
        'type',
        'amount_cents',
        'reason',
        'authorized_by_pin',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }
}
