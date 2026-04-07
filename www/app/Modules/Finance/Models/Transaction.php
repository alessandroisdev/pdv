<?php

namespace App\Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\ValueObjects\Money;

class Transaction extends Model
{
    protected $fillable = [
        'actor_id', 'actor_type', 'type', 
        'amount_cents', 'payment_method', 
        'source_id', 'source_type'
    ];

    public function actor()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function getAmountAttribute(): Money
    {
        return new Money((int) $this->amount_cents);
    }

    public function setAmountAttribute(Money $money): void
    {
        $this->attributes['amount_cents'] = $money->getCents();
    }
}
