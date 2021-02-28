<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Models\{User,Admin};

class Filerequest extends Model
{
    use HasFactory;

    protected $table = 'filerequest';

    /**
     * The one,who belongs the File
     * Either Admin or User
     */
    public function owner()
    {
        return $this->hasOne('App\Models\User', 'user_owner_id') ??
             $this->hasOne('App\Models\Admin', 'user_owner_id');
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

    public function file()
    {
        return $this->belongsTo('App\Models\File');
    }
}
