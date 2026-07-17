<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'study_program_id', 'cis_pegawai_id', 'nip', 'alias', 'nidn',
    'jabatan_fungsional', 'jenjang_pendidikan',
    'expertise', 'photo', 'is_public',
    ];

    /**
     * Pilihan jabatan fungsional akademik dosen (Permendikbud No. 92 Tahun 2014)
     */
    public static function jabatanFungsionalOptions(): array
    {
        return [
            'Asisten Ahli',
            'Lektor',
            'Lektor Kepala',
            'Guru Besar / Profesor',
        ];
    }

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // === RELASI ===
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function researches()
    {
        return $this->hasMany(Research::class);
    }

    public function communityServices()
    {
        return $this->belongsToMany(CommunityService::class, 'lecturer_community_service')
                     ->withPivot('role')
                     ->withTimestamps();
    }

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    public function books() { return $this->hasMany(Book::class); }
    public function hkis() { return $this->hasMany(Hki::class); }
    public function awards() { return $this->hasMany(Award::class); }

    // === SCOPE: hanya data publik ===
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // === HELPER: foto URL ===
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }
}
