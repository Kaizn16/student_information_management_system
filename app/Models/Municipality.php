<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $table = 'municipalities';
    protected $primaryKey = 'municipality_id';
    protected $fillable = [
        'province_id',
        'municipality_name'
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
