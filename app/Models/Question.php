<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function answer()
    {
        return $this->hasOne(TeacherAnswer::class);
    }

    public function isMCQ()
    {
        return $this->type === 'mcq';
    }

    public function isOpenEnded()
    {
        return $this->type === 'open_ended';
    }
}
