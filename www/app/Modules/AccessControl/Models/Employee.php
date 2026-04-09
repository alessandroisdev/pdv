<?php

namespace App\Modules\AccessControl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Modules\Core\Traits\HasBranchScope;

class Employee extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable, HasBranchScope;

    protected $fillable = [
        'name',
        'pin',
        'level',
        'user_id',
        'status',
        'cpf',
        'rg',
        'admission_date',
        'base_salary_cents',
        'role_description',
        'contact_phone',
        'address',
        'bank_account_info',
        'work_schedule',
        // Advanced HR Fields
        'birth_date',
        'gender',
        'marital_status',
        'rg_issuer',
        'pis_pasep',
        'ctps_number',
        'cep',
        'city',
        'state',
        'neighborhood',
        'address_number',
        'department',
        'contract_type',
        'pix_key',
        'emergency_contact_name',
        'emergency_contact_phone',
        'branch_id'
    ];

    protected $casts = [
        'admission_date' => 'date',
        'birth_date' => 'date',
        'base_salary_cents' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
