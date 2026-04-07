<?php

namespace App\Modules\AccessControl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FunctionalProfile extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'document', 'pin_hash', 'status'];
    protected $hidden = ['pin_hash'];
}
