<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;


    public function departmentadmin()
    {
        return $this->hasMany('App\Models\Admin');
    }

    public function departmentuser()
    {
        return $this->hasMany('App\Models\User');
    }

    public function departmentdocument()
    {
        return $this->hasMany('App\Models\Departmentdocument');
    }
}
