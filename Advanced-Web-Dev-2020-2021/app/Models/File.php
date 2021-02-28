<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'file';


    public function user()
    {
        return $this->hasOne('App\Models\User','user_owner_id');
    }

    public function admin()
    {
        return $this->hasOne('App\Models\Admin');
    }

    public function sharedfile()
    {
        return $this->hasMany(Sharedfile::class,'file_id');
    }

    public function filerequest()
    {
        return $this->hasMany('App\Models\Filerequest');
    }

    public function keyword()
    {
        return $this->hasMany('App\Models\Keyword');
    }

    public function files()
    {
        return $this->hasMany(File::class,'parentfolder_id');
    }

}
