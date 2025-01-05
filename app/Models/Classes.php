<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;
    protected $table = 'classes';

    protected $primaryKey = 'class_id';

    protected $fillable = [
        'class_name',
        'room_id',
        'teacher_id',
        'subject_id',
        'year_level',
        'section',
        'students',
        'schedule_day_time',
    ];

    protected $casts = [
        'students' => 'array',
        'schedule_day_time' => 'array',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    
    public function teacher()
    {
        return $this->belongsTo(Teacher::class,'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class,'subject_id');
    }

    public function getFormattedScheduleDayTimeAttribute()
    {
        $dayMap = [
            'monday' => 'MON',
            'tuesday' => 'TUE',
            'wednesday' => 'WED',
            'thursday' => 'THU',
            'friday' => 'FRI',
            'saturday' => 'SAT',
            'sunday' => 'SUN',
        ];

        $formattedSchedule = collect($this->schedule_day_time)
            ->map(function ($item) use ($dayMap) {
                $start = Carbon::parse($item['time_from'])->format('g:i A');
                $end = Carbon::parse($item['time_to'])->format('g:i A');
                return "{$dayMap[$item['day']]} {$start} - {$end}";
            })
            ->implode('<br>');

        return $formattedSchedule;
    }
}
