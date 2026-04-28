<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $fillable = ['lecturer_id','title','year','publisher','publisher_url','visibility'];
    
    protected $table = 'publications';

    public function lecturer() { return $this->belongsTo(Lecturer::class); }

    public function scopePublic($query) { return $query->where('visibility','public'); }

    // Filter by year - kebutuhan Pak Febrian (latest publication)
    public function scopeLatest($query, $year = null)
    {
        if ($year) return $query->where('year', $year);
        return $query->orderBy('year','desc');
    }
}
