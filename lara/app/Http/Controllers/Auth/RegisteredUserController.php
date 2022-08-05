<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Billing;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $parent=Session::get('parent');
        if(!$parent){
            return redirect()->route('invite');
        }else{
            return view('auth.register');
        }
        //
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $parent=Session::get('parent');
        if(!$parent){
            return redirect()->route('invite');
        }else{
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $invite_token=$this->gen_invite_token();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'invite_token' => $invite_token,
                'parent_id' => $parent->id,
                'parent_invite_token' => Session::get('invite_token'),
            ]);
    
            event(new Registered($user));
            $parent->invite_count++;
            $parent->save();
            //Auth::login($user);
            return view('auth.verify-email');
            //return redirect(RouteServiceProvider::HOME);
        }
    }
    
    private function check_invite_token($token){
        $pdo=DB::connection()->getPdo();
        $token=$pdo->quote($token);
        $sql="SELECT invite_token FROM users WHERE invite_token={$token} UNION SELECT invite_token FROM adm_invites WHERE invite_token={$token} UNION SELECT invite_token FROM user_invites WHERE invite_token={$token}";
        $istoken=$pdo->query($sql)->fetch();
        if(!$istoken) return true; else return false;
    }
    
    private function gen_invite_token($count=10){
        $istoken=false;
        $k=0;
        while(!$istoken){ // || $k<100
            $token=Str::random($count);
            $istoken=$this->check_invite_token($token);
            $k++;
        }
        return $token;
    }
}
