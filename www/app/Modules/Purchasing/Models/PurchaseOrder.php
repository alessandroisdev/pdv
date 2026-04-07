<?php

namespace App\Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\ValueObjects\Money;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = ['supplier_id', 'notes', 'status', 'total_cost_cents'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getTotalCostAttribute(): Money
    {
        return new Money((int) $this->total_cost_cents);
    }

    public function setTotalCostAttribute(Money $money): void
    {
        $this->attributes['total_cost_cents'] = $money->getCents();
    }
}
