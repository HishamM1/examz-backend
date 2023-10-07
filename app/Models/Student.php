<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class,'teacher_student','student_id','teacher_id')->withTimestamps();
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'student_exam')->withTimestamps()->withPivot(['total_score', 'status']);
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function likes()
    {
        return $this->hasMany(AnnouncementLike::class);
    }

    public function views()
    {
        return $this->hasMany(AnnouncementView::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($query) use ($search) {
            $query->where('full_name', 'like', "%$search%");
        });
    }
}
