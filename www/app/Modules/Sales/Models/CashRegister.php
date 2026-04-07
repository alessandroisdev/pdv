<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = [
        'terminal_identifier', 'opened_by_id', 'opened_by_type',
        'opened_at', 'closed_at', 'initial_cents', 'final_cents', 'status'
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
