<?php
// ============================================================
// Education.php
// ============================================================
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = ['lecturer_id','degree','institution','major','year'];

    protected $table = 'educations';

    public function lecturer() { return $this->belongsTo(Lecturer::class); }
}
