<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdmInvite;
use App\Models\UserInvite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InviteUserController extends Controller

{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.invite');
    }
    
    public function check(Request $request)
    {
        $isadmin = AdmInvite::where('invite_token',$request->invite)->first();
        if(isset($isadmin)){
            $parent = User::where('id',1)->first();
            //$parent->invite_token=$request->invite;
        }else{
            $parent = User::where('invite_token',$request->invite)->first();
            if(!$parent){
                $user_invite = UserInvite::where('invite_token',$request->invite)->first();
                $parent = User::where('id',$user_invite->user_id)->first();
                //$parent->invite_token=$request->invite;
            }
        }
        
        if(isset($parent->id)){
            if( ($parent->invite_limit>0 && $parent->invite_count < $parent->invite_limit) || $parent->id==1){
                Session::put('parent', $parent);
                Session::put('invite_token', $request->invite);
                return redirect()->route('register');            
            }else{
                return view('auth.invite')->with('noinvite','This invite will activated only with active subscription');
            }
        }else{
            Session::forget('parent');
            Session::forget('invite_token');
            return view('auth.invite')->with('noinvite','No such invite');
        }
    }
}
