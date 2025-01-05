<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use SoftDeletes;

    protected $table = 'rooms';

    protected $primaryKey = 'room_id';

    protected $fillable = [
        'room_name',
        'max_seat',
        'building_name',
        'building_description',
    ];

}