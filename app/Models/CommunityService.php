<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityService extends Model
{
    protected $fillable = ['title', 'year', 'month', 'location', 'pkm_type', 'pkm_scheme', 'student_members', 'visibility', 'input_by_lppm_id'];

    protected $table = 'community_services';

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_community_service')
                     ->withPivot('role')
                     ->withTimestamps();
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }
}
