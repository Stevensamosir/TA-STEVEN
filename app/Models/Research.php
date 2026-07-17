<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Research extends Model
{
    protected $fillable = ['lecturer_id','title','year','month','funding_source','input_by_lppm_id'];

    protected $table = 'researches';

    public function lecturer() { return $this->belongsTo(Lecturer::class); }

}
