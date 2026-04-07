<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Models\Product;
use App\Modules\Core\ValueObjects\Money;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'quantity', 'unit_price_cents'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUnitPriceAttribute(): Money
    {
        return new Money((int) $this->unit_price_cents);
    }
}
