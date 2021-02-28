<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Folder extends Model
{
    use HasFactory;

    //tablename in database
    protected $table = 'folder';

    public function getNextId()
    {
        $statement = DB::select("show table status like 'folder' ");
        return $statement[0]->Auto_increment;
    }

    public function files()
    {
        return $this->hasMany(File::class,'parentfolder_id');
    }

    public function folders()
    {
        return $this->hasMany(Folder::class,'parentfolder_id');
    }
}
