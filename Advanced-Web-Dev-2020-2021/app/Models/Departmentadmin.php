<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departmentadmin extends Model
{
    use HasFactory;

    public $timestamps = false;

    //tablename in database
    protected $table = 'departmentadmin';

    public function admin()
    {
        return $this->hasOne('App\Models\Admin');
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
