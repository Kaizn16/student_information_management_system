<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{

    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'teachers';
    protected $primaryKey = 'teacher_id';
    protected $fillable = [
        'user_id',
        'faculty_uid',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'age',
        'civil_status',
        'date_of_birth',
        'nationality',
        'contact_no',
        'email',
        'region_id',
        'province_id',
        'municipality_id',
        'barangay_id',
        'street_address',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_no',
        'tin_id',
        'sss_number',
        'pagibig_number',
        'philhealth_number',
        'prc_license_number',
        'prc_license_expiration_date',
        'highest_degree',
        'field_of_specialiation',
        'university_graduated_name',
        'year_graduated',
        'additional_course_training',
        'designation',
        'subjects_handle',
        'employment_type',
        'date_hired',
        'employment_status',
    ];

    protected $casts = [
        'subjects_handle' => 'array',
    ];

    public static function booted()
    {
        static::saved(function ($record) {
            if ($record->wasRecentlyCreated) {
                $user = User::create([
                    'username' => $record->faculty_uid,
                    'name' => $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name,
                    'email' => $record->email,
                    'password' => Hash::make($record->password),
                    'role_id' => 2,
                ]);
            
                $teacher = Teacher::where('email', $record->email)->first();
                
                if ($teacher) {
                    $teacher->update([
                        'user_id' => $user->user_id,
                    ]);
                }
            }
        }); 
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public Function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }

}
