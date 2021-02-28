<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sharedfolder extends Model
{
    use HasFactory;

    //tablename in database
    protected $table = 'sharedfolder';

    public function folder()
    {
        // return $this->belongsTo(Folder::class,'parentfolder_id');
        return $this->belongsTo(Folder::class,'folder_id');
    }
}
