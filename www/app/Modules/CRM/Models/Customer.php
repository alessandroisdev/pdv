<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'address',
        'is_club_member',
        'lgpd_consent',
    ];

    protected $casts = [
        'is_club_member' => 'boolean',
        'lgpd_consent' => 'boolean',
    ];
}
