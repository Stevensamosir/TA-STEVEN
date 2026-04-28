<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'head_lecturer_id'];

    // === RELASI ===
    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }

    // Kaprodi prodi ini
    public function headLecturer()
    {
        return $this->belongsTo(Lecturer::class, 'head_lecturer_id');
    }
}
