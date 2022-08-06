<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $user=Auth::user();
        if($user->no2fa){

            $request->session()->regenerate();
    
            return redirect()->intended(RouteServiceProvider::HOME);
        }else{
            $request->session()->put('auth_user',$user->id);
            $user->code=rand(1000,9999);
            $request->session()->put('auth_code',$user->code);
            $user->save();
            Mail::to($user->mail)->send(new codeMail(['code'=>$user->code]));
            Auth::guard('web')->logout();
            
            return redirect('code');
        }
    }
    
    public function code(Request $request){
        if($request->session()->has('auth_user')){
            return view('auth.code');
        }
    }
    
    public function codestore(Request $request){
        if($request->session()->has('auth_user') && $request->post('code')==$request->session()->get('auth_code') ){
            $user=User::where('id', $request->session()->get('auth_user'))->where('code', $request->session()->get('auth_code'))->first();
            if($user){
                Auth::login($user);
                $request->session()->forget('auth_user', 'auth_code');
                $request->session()->regenerate();
                return redirect()->intended(RouteServiceProvider::HOME);
            }
        }else{
            return view('auth.code')->with('wrongcode','Wrong code');
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