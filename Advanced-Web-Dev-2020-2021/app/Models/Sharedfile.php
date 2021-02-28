<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sharedfile extends Model
{
    use HasFactory;

    protected $table = 'sharedfile';

    /**
     * The one,who belongs the File
     * Either Admin or User
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'user_owner_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class,'file_id');
    }



    /**
     * The one,who is requesting the File
     * Either Admin or User
     */
    public function requester()
    {
        return $this->hasOne('App\Models\User', 'user_requester_id') ??
            $this->hasOne('App\Models\Admin', 'user_requester_id');
    }

}
