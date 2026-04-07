<?php

namespace App\Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;

class LabelTemplate extends Model
{
    protected $fillable = [
        'name',
        'width_mm',
        'height_mm',
        'layout_html',
    ];
}
