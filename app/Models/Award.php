<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $fillable = ['lecturer_id','name','level','organizer','rank','date','evidence_url'];
    protected $table = 'awards';
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
}
