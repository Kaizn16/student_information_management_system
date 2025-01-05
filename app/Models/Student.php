<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'students';
    protected $primaryKey = 'student_id';
    protected $fillable = [
        'user_id',
        'student_uid',
        'lrn',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex',
        'age',
        'date_of_birth',
        'place_of_birth',
        'nationality',
        'region_id',
        'province_id',
        'municipality_id',
        'barangay_id',
        'street_address',
        'contact_no',
        'email',
        'father_first_name',
        'father_middle_name',
        'father_last_name',
        'father_occupation',
        'father_contact_no',
        'mother_first_name',
        'mother_middle_name',
        'mother_last_name',
        'mother_occupation',
        'mother_conact_no',
        'guardian_fisrt_name',
        'guardian_middle_name',
        'guardian_last_name',
        'guardian_occupation',
        'guardian_contact_no',
        'guardian_relation',
        'previous_school_name',
        'birth_certificate',
        'teacher_id',
        'report_card',
        'current_year_level',
        'strand_id',
        'section',
        'school_year',
        'enrollment_status'
    ];

    public static function booted()
    {
        static::saved(function ($record) {
            if ($record->wasRecentlyCreated) {
                $user = User::create([
                    'username' => $record->student_uid,
                    'name' => $record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name,
                    'email' => $record->email,
                    'password' => Hash::make($record->password),
                    'role_id' => 3, // student role
                ]);
            
                $student = Student::where('email', $record->email)->first();
                
                if ($student) {
                    $student->update([
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

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function strand()
    {
        return $this->belongsTo(Strand::class, 'strand_id');
    }
}
