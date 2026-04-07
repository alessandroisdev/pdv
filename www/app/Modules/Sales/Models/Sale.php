<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\ValueObjects\Money;

class Sale extends Model
{
    protected $fillable = [
        'cash_register_id',
        'seller_id',
        'seller_type',
        'customer_id',
        'customer_document',
        'total_cents',
        'discount_cents',
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function seller()
    {
        return $this->morphTo('seller');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Modules\CRM\Models\Customer::class);
    }

    public function getTotalAttribute(): Money
    {
        return new Money((int) $this->total_cents);
    }
}
