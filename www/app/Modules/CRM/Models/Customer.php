<?php

namespace App\Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'birth_date',
        'points', // Programa de Fidelidade Clube de Vantagens
        'last_purchase_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'last_purchase_date' => 'date',
    ];
}
