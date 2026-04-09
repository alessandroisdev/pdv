<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\Core\Traits\HasBranchScope;

class Opportunity extends Model
{
    use SoftDeletes, HasBranchScope;

    protected $table = 'crm_opportunities';

    protected $fillable = [
        'title', 'customer_id', 'branch_id', 'owner_id', 
        'stage', 'amount_cents', 'notes', 'expected_close_date', 'order'
    ];

    protected $casts = [
        'expected_close_date' => 'date',
        'amount_cents' => 'integer'
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Modules\CRM\Models\Customer::class);
    }

    public function owner()
    {
        return $this->belongsTo(\App\Modules\AccessControl\Models\Employee::class, 'owner_id');
    }
}
