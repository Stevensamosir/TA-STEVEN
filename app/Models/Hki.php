<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hki extends Model
{
    protected $fillable = ['lecturer_id','title','year','type','certificate_number'];
    protected $table = 'hkis';
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
}
