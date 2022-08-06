<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Mail;
use App\Mail\codeMail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(!Session::has('uid')){
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
     
            Auth::attemptWhen($credentials, function ($user) {
                Session::put('uid',$user->id);
                $user->code=rand(1000,9999);
                $user->save();

                Mail::to($user->mail)->send(new codeMail(['code'=>$user->code]));

                return false;
            });
            return view('auth.code');
        
        }else{
            $post=$request->validate(['code'=>['required']]);
            $user=User::where('id',Session::get('uid'))->where('code',$post['code'])->first();
            if($user){
                Auth::login($user);
                $request->session()->regenerate();
                $user->code=NULL;
                $user->save();
                return redirect()->intended(RouteServiceProvider::HOME);
            }
            //Auth::attempt(['id' => Session::get('uid'), 'code' => $post['code']]);
        }
        
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
