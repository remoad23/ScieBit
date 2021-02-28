<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    //to allow mass assignment
    protected $fillable = ['admin-token','email','name','lastname','password','picture'];

    protected $table = 'admin';

    public function file()
    {
        return $this->HasMany(File::class,'admin_owner_id');
    }

    public function folder()
    {
        return $this->HasMany(Folder::class,'admin_owner_id');
    }

    public function sharedfile()
    {
        return $this->HasMany(Sharedfile::class,'admin_requester_id');
    }

    public function sharedfolder()
    {
        return $this->HasMany(Sharedfolder::class,'admin_requester_id');
    }
}
