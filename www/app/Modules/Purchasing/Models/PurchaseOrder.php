<?php

namespace App\Modules\Purchasing\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Inventory\Models\Product;
use App\Models\User;
use App\Modules\Core\ValueObjects\Money;
use OwenIt\Auditing\Contracts\Auditable;

class PurchaseOrder extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'status',
        'invoice_number',
        'total_cents',
        'notes',
        'received_at'
    ];

    protected $casts = [
        'received_at' => 'datetime'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getTotalAttribute(): Money
    {
        return new Money((int) $this->total_cents);
    }
}
