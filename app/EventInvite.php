<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EventInvite extends Model
{
    protected $table = 'event_invites';
    public $timestamps  = false;
    protected $fillable = [
         'eid', 'attendee_id','status'
    ];
//    protected $primaryKey = ['eid','attendee_id'];
    public function events(){
        return $this->hasMany('App\EventEntry','eid','eid');
    }
    protected function setKeysForSaveQuery(Builder $query)
    {
        return $query->where('eid', $this->getAttribute('eid'))
            ->where('attendee_id', $this->getAttribute('attendee_id'));
    }
}
