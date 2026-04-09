<?php

namespace App\Modules\Fiscal\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    protected $table = 'tax_rules';

    protected $guarded = ['id'];

    public $timestamps = true;
    
    protected $casts = [
        'icms_rate' => 'float',
        'icms_st_margin' => 'float',
        'pis_rate' => 'float',
        'cofins_rate' => 'float',
        'has_st' => 'boolean',
        'is_active' => 'boolean',
    ];
}
