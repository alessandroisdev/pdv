<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\ValueObjects\Money;

class Sale extends Model
{
    protected $fillable = ['cash_register_id', 'seller_id', 'seller_type', 'total_cents'];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function seller()
    {
        return $this->morphTo('seller');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getTotalAttribute(): Money
    {
        return new Money((int) $this->total_cents);
    }
}
