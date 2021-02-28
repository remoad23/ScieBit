<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    use HasFactory;

    protected $table = 'keyword';


    public function file()
    {
        return $this->hasOne('App\Models\File', 'file_id');
    }
}
