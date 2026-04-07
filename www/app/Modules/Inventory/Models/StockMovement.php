<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    const UPDATED_AT = null; // Disable updated_at to enforce append-only style

    protected $fillable = [
        'product_id', 'actor_id', 'actor_type', 
        'quantity', 'type', 'transaction_motive'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function actor()
    {
        return $this->morphTo();
    }
}
