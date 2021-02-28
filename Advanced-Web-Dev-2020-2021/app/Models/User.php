<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    //to allow mass assignment
    protected $fillable = ['user-token','name','lastname','password','email','picture'];

    //tablename in database
    protected $table = 'user';

    public function file()
    {
        return $this->HasMany('App\Models\File','user_owner_id');
    }

    public function folder()
    {
        return $this->HasMany(Folder::class,'user_owner_id');
    }

    public function sharedfile()
    {
        return $this->HasMany('App\Models\Sharedfile','user_requester_id');
    }

    public function sharedfolder()
    {
        return $this->HasMany(Sharedfolder::class,'user_requester_id');
    }

    public function departmentfile()
    {
        return $this->HasMany('App\Models\DepartmentFile');
    }

    public function departmentuser()
    {
        return $this->HasMany('App\Models\DepartmentUser');
    }
}
