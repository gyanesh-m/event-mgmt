<?php

namespace App\Http\Controllers;

use App\EventInvite;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;

class InviteController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get query
        $query = $request->query();
        //Validate for event id
        $validator = Validator::make($query,[
            'id'=>'numeric|min:1'
        ]);

        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        // Validate for email id
        $content = json_decode($request->getContent(),true);
        $validator = Validator::make($content,[
            "invitees"    => "required|array|min:1",
            "invitees.*"  => "required|email|distinct",
        ]);
        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        $eid = $query['id'];
        $invitees = $content['invitees'];
        foreach($invitees as $val){
            $invite = new EventInvite;
            $invite->eid = $eid;
            $userid = User::where('email',$val)->get()->first()->id;
            $invite->attendee_id = $userid;
            $invite->status = 'Pending';
            $invite->saveOrFail();
        }
        return "Users in the list are invited.";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        $query = $request->query();
        //Validate for event id in query
        $validator = Validator::make($query,[
            'id'=>'numeric|min:1'
        ]);

        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        // Get user id.
        $email = $request->header()['php-auth-user'][0];
        $user = User::where('email',$email)->get()->first();
        $key_list = array_keys($query);
        if(in_array('id',$key_list)){
            $invite = $user->invites->where('eid',$query['id']);
        }
        else{
        $invite = $user->invites;
        }
        return $invite;
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
        // Get query
        $query = $request->query();
        //Validate for event id
        $validator = Validator::make($query,[
            'id'=>'numeric|min:1'
        ]);

        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        // Validate for status
        $content = json_decode($request->getContent(),true);
        $validator = Validator::make($content,[
            'status'=>['required',
                Rule::in(['accepted','pending','rejected'])
            ]
        ]);
        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        try {
            $eid = $query['id'];
            $email = $request->header()['php-auth-user'];
            $user = User::where('email', $email)->get()->first()->id;
            $einvite = EventInvite::where('eid',$eid)->where('attendee_id',$user)->get()->first();
            $einvite['status'] = $content['status'];
            $einvite->save();
        }
        catch(ModelNotFoundException $e){
            return $e;
        }
        return response("Invited id: ".$eid." Updated!",200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // Query validation
        $query = $request->query();
        $validator = Validator::make($query,[
            'id'=>'required|numeric|min:1'
        ]);
        if($validator->fails()){
            return redirect('events')->withErrors($validator)
                ->withInput();
        }
        // Event id
        $eid = $query['id'];
        // User id
        $email = $request->header()['php-auth-user'];
        $user = User::where('email',$email)->get()->first();
        $userid = $user->id;
        //Event Invite
        $einvite = EventInvite::where('eid',$eid)->where('attendee_id',$userid);
        $einvite->delete();
        return response ("Event invite with id: ".$eid." deleted.",200);
    }
}
