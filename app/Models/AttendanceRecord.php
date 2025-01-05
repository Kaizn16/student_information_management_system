<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use SoftDeletes;

    protected $table = "attendance_records";
    protected $primaryKey = "attendance_record_id";
    protected $fillable = [
        'class_id',
        'attendance_date',
        'present_students',  
    ];

    protected $casts = [
        'present_students' => 'array',
    ];

    public function classes()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

}
