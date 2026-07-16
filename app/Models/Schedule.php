<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['lecturer_id','day','start_time','end_time','description','status'];
    protected $table = 'schedules';
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
}
