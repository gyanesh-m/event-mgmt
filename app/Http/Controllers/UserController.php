<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use function Sodium\add;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {

        $this->middleware('basic_a')->except(['store']);
    }
    public function index()
    {
        //


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        "ADD VALIDATION.";
        $decode_req = json_decode($request->getContent(),true);
        $user =  new User;
        $user = $this->addInfo($decode_req,$user);
        try {
            $user->saveOrFail();
        }
        catch(\Exception $e)
        {
            return abort(400,$e->getMessage());
        }
        return "New user saved";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request )
    {
        //
//        dd($request);
//        $this->middleware('basic_a');
        $email = $request->header()['php-auth-user'];
        $user = User::where('email',$email)->get()->first();
        return $user;
//        return "showing user 1";
//        dd($user);
//        dd("testing");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //

        $email = $request->header()['php-auth-user'][0];
        $decode_req = json_decode($request->getContent(),true);
        $user = User::where('email',$email)->get()->first();
        $this->addInfo($decode_req,$user);
//        foreach($decode_req as $key=>$val)
//            if($key!='id' || $key!='is_admin')
//                $user->$key = $val;
//
        $user->save();
        return "Info updated";
//        dd($user);

    }
    public function addInfo($decode_req,User $user)
    {
        $haystack = ["name", "email", "phone_number", "password","isd"];
        foreach ($decode_req as $key => $val){
            if (in_array($key, $haystack))
                if ($key == 'password')
                    $user->$key = Hash::make($decode_req['password']);
                else
                    $user->$key = $val;
        }
        return $user;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $email = $request->header()['php-auth-user'][0];
        $user = User::where('email',$email)->get()->first();
        $user->delete();
//        User::destroy($email);
        return 'deleted  '.$email;

    }
}
