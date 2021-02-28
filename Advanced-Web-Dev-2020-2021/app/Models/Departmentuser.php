<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departmentuser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'departmentuser';

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }

    public function departmentdocuments()
    {
        return $this->hasMany('App\Models\Departmentdocument');
    }
}
