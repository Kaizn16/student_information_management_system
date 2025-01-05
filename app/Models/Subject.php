<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{

    use SoftDeletes;
    protected $table = 'subjects';
    protected $primaryKey = 'subject_id';
    protected $fillable = [
        'strand_id', 'subject_code', 'subject_title', 'subject_description'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function strand() 
    {
        return $this->belongsTo(Strand::class, 'strand_id');
    }

}
