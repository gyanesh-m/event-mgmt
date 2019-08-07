<?php

namespace App\Http\Controllers;

use App\EventEntry;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation for content
        $decode_req = json_decode($request->getContent(),true);
        $validator = Validator::make($decode_req,[
            'start_time'=>'required|numeric',
            'end_time'=>'required|numeric',
            'venue'=>'required|string|max:256',
            'topic'=>'required|string|max:256',
            'description'=>'required|string|max:512',
            'isd' => 'required|string|max:4',
            'contact'=>'required|digits:10',
        ]);
        // Validation check
        if($validator->fails()){
            return redirect('events')
                ->withErrors($validator)
                ->withInput();
        }
        // Create new event.
        $event =  new EventEntry;
        $email = $request->header()['php-auth-user'][0];
        $user = User::where('email',$email)->get()->first();
        // Fetch user id
        $userid = $user->id;
        // Add the details to event
        $haystack = ["start_time","end_time","venue","topic","description","isd","contact"];
        foreach($decode_req as $key=>$val)
            if(in_array($key,$haystack))
                $event->$key = $val;
        $event->hostid = $userid;

        $event->save();
        return "Event saved!";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show( Request $request)
    {
        $query = $request->query();
        // Validation for query
        $validator = Validator::make($query,[
            'id'=>'numeric|min:1',
            'status'=>[
                Rule::in(['accepted','pending','rejected'])
                ]
        ]);
        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        try {
            $email = $request->header()['php-auth-user'];
            $user = User::where('email',$email)->get()->first();
            $event_list = $user->events;
            $key_list = array_keys($query);
            // Fetch events only by id
            if(in_array('id',$key_list)){
                $result = $event_list->where('eid',$query['id']);
                return $result;
            }
            else{
                // Fetch all events.
                $result = $event_list;
                if(!in_array('status',$key_list)){
                    return $result;
                }
            }
            // Fetch events by status
            if(in_array('status',$key_list)){
                $result = $user->invites->where('status',$query['status']);
                $res = array();
                foreach($result as $val){
                    $event_detail = $val->events;
                    array_push($res,$event_detail);
                }
                return $res;
            }

        }
        catch(ModelNotFoundException $e){
            return redirect('events')
                ->withErrors($e->getMessage())
                ->withInput();
        }
        }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validation for Content
        $decode_req = json_decode($request->getContent(),true);
        $validator = Validator::make($decode_req,[
            'start_time'=>'required|numeric',
            'end_time'=>'required|numeric',
            'venue'=>'required|string|max:256',
            'topic'=>'required|string|max:256',
            'description'=>'required|string|max:512',
            'isd' => 'required|string|max:4',
            'contact'=>'required|digits:10',
        ]);
        // Validation check
        if($validator->fails()){
            return redirect('events')
                ->withErrors($validator)
                ->withInput();
        }
        // Get event
        try {
            $eid = $request->query()['id'];
            $event = EventEntry::where('eid',$eid)->firstOrFail();

        }
        catch(ModelNotFoundException $e){
            return redirect('events')
                ->withErrors($e->getMessage())
                ->withInput();
        }
        // Update event
        $haystack = ["start_time","end_time","venue","topic","description","isd","contact"];
        foreach($decode_req as $key=>$val) {
            if (in_array($key, $haystack))
                $event->$key = $val;
        }
        $event->save();
        print_r("Updated event id:".$eid);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request)
    {
        // Validate query
        $query = $request->query();
        $validator = Validator::make($query,[
            'id'=>'required|numeric|min:1'
        ]);
        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        // Fetch id
        try {
            $eid = $query['id'];
            $event = EventEntry::where('eid',$eid)->firstOrFail();
        }
        catch(ModelNotFoundException $e){
            return redirect('events')
                ->withErrors($e->getMessage())
                ->withInput();
        }
        // Delete event.
        $statement = "Deleted Event with topic:".$event->topic." and having event_id: ".$event->eid;
        $event->delete();
        return $statement;
    }
}
