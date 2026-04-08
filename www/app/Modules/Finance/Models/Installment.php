<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $fillable = [
        'type',
        'description',
        'amount_cents',
        'due_date',
        'paid_date',
        'status',
        'transaction_id'
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
