<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $fillable = ['lecturer_id','name','level','organizer','rank','date','evidence_url','visibility'];
    protected $table = 'awards';
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
    public function scopePublic($query) { return $query->where('visibility','public'); }
}
