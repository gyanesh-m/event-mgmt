<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventEntry extends Model
{
    protected $table = 'event_entry';
    public $timestamps  = false;
    protected $primaryKey = 'eid';

    //
    public function invites(){
        return $this->hasMany('App\EventInvite','hostid','id');
    }

}
