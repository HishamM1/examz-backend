<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function views()
    {
        return $this->hasMany(AnnouncementView::class);
    }

    public function likes()
    {
        return $this->hasMany(AnnouncementLike::class);
    }
}
