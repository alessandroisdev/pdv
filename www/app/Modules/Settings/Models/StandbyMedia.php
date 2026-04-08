<?php

namespace App\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class StandbyMedia extends Model
{
    protected $table = 'standby_medias';

    protected $fillable = [
        'type',
        'file_path',
        'sort_order',
        'duration_seconds',
    ];
}
