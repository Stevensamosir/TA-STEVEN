<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['lecturer_id','title','year','publisher','isbn'];
    protected $table = 'books';
    public function lecturer() { return $this->belongsTo(Lecturer::class); }
}
