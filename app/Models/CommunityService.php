<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CommunityService extends Model
{
    protected $fillable = ['lecturer_id','title','year','location','visibility'];

    protected $table = 'community_services';

    public function lecturer() { return $this->belongsTo(Lecturer::class); }

    public function scopePublic($query) { return $query->where('visibility','public'); }
}
