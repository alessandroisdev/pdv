<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Core\Traits\HasBranchScope;

class StockMovement extends Model
{
    use HasBranchScope;

    const UPDATED_AT = null; // Disable updated_at to enforce append-only style

    protected $fillable = [
        'product_id', 'actor_id', 'actor_type', 
        'quantity', 'type', 'transaction_motive', 'branch_id'
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
