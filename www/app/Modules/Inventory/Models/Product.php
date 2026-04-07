<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\ValueObjects\Money;

class Product extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name', 'sku', 'barcode', 'description', 'category_id',
        'price_cents_cost', 'price_cents_sale', 'price_cents_club', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Calculated value getter. In heavy scenarios we can cache this value.
     */
    public function getCurrentStockAttribute(): int
    {
        return $this->stockMovements()->sum('quantity');
    }

    // --- Object Calisthenics conversions via custom attributes ---

    public function getCostPriceAttribute(): Money
    {
        return new Money((int) $this->price_cents_cost);
    }

    public function setCostPriceAttribute(Money $money): void
    {
        $this->attributes['price_cents_cost'] = $money->getCents();
    }

    public function getSalePriceAttribute(): Money
    {
        return new Money($this->price_cents_sale);
    }

    public function getClubPriceAttribute(): ?Money
    {
        if (is_null($this->price_cents_club)) return null;
        return new Money($this->price_cents_club);
    }

    public function setSalePriceAttribute(Money $money): void
    {
        $this->attributes['price_cents_sale'] = $money->getCents();
    }
}
