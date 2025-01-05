<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{ 

    protected $table = 'provinces';
    protected $primaryKey = 'province_id';
    protected $fillable = [
        'region_id',
        'province_name'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
