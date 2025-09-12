<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TCisterna extends Model
{
    protected $table = 't_cisternas';
    protected $primaryKey = 'id';
    public $timestamps = false; 

    protected $fillable = [
        'fecha',
        'cisterna_id',
        'nivel',
        'eth',
    ];
}
