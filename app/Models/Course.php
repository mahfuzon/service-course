<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'certificate', 'thumbnail', 'type',
        'status', 'price', 'level', 'description', 'mentor_id'
    ];

    public function mentor()
    {
        return $this->belongsTo(Mentor::class);
    }

    public function chapter()
    {
        return $this->hasMany(Chapter::class)->orderBy('id', 'ASC');
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function image()
    {
        return $this->hasMany(ImageCourse::class)->orderBy('id', 'DESC');
    }

    protected $casts = [
        "created_at" => 'datetime: Y-m-d H:m:s',
        "updated_at" => 'datetime: Y-m-d H:m:s'
    ];
}
