<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Exam extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_exam')->withPivot(['total_score', 'status'])->withTimestamps();
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function isVisible()
    {
        return $this->visible;
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    public function teacherAnswers()
    {
        return $this->hasMany(TeacherAnswer::class);
    }

    public function scopeActive(Builder $query)
    {
        // active exams each exam start time has come and exam end time has not come yet or null
        return $query->where(function ($query) {
            $query->where('start_time', '<=', now())->where(function ($query) {
                $query->where('end_time', '>=', now())->orWhereNull('end_time');
            });
        });
    }

    public function scopeEnded(Builder $query)
    {
        return $query->where(function ($query) {
            $query->where('start_time', '>', now())->orWhere('end_time', '<', now());
        });
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%');
            });
        })->when($filters['active'] ?? false, function ($query, $value) {
            if($value == 'active') {
                $query->active();
            } elseif($value == 'ended') {
                $query->ended();
            }
        })->when($filters['sortBy'] ?? false, function ($query, $sortBy) {
            if ($sortBy == 'newest') {
                $query->latest();
            } elseif ($sortBy == 'oldest') {
                $query->oldest();
            } elseif ($sortBy == 'shortest') {
                $query->orderBy('duration');
            } elseif ($sortBy == 'longest') {
                $query->orderByDesc('duration');
            }
        })->when($filters['status'] ?? false, function ($query, $status) {
            // return student exams with the given status
            if($status == 'taken') {
                $query->whereHas('students', function ($query) {
                    $query->where('student_id', auth()->user()->student->id)->where('status', 'finished');
                });
            } elseif($status == 'in_progress') {
                $query->whereHas('students', function ($query) {
                    $query->where('student_id', auth()->user()->student->id)->where('status', 'started');
                });
            } elseif($status == 'not_taken') {
                $query->whereDoesntHave('students', function ($query) {
                    $query->where('student_id', auth()->user()->student->id);
                });
            }

        });
    }
}
