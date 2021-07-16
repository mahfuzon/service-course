<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'course_id'];

    public function lesson()
    {
        return $this->hasMany(Lesson::class)->orderBy('id', 'ASC');
    }
    protected $casts = [
        "created_at" => 'datetime: Y-m-d H:m:s',
        "updated_at" => 'datetime: Y-m-d H:m:s'
    ];
}
