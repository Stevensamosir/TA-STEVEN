<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    protected $fillable = ['lecturer_id','title','year','funding_source','visibility'];

    protected $table = 'researches';

    public function lecturer() { return $this->belongsTo(Lecturer::class); }

    public function scopePublic($query) { return $query->where('visibility','public'); }
}
