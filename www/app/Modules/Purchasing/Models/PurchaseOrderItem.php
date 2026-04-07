<?php

namespace App\Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Models\Product;
use App\Modules\Core\ValueObjects\Money;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity', 'unit_cost_cents'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function getUnitCostAttribute(): Money
    {
        return new Money((int) $this->unit_cost_cents);
    }
}
