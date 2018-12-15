<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the Discord authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('discord')->redirect();
    }

    /**
     * Obtain the user information from Discord.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::with('discord')->stateless()->user();

        if (User::query()->where('discord_id', '=', $user->id)->first()) {
            $checkUser = User::query()->where('discord_id', '=', $user->id)->first();
            Auth::login($checkUser);

            return redirect(route('dashboard'));
        }

        $newUser                 = new User();
        $newUser->name           = $user->name;
        $newUser->email          = $user->email;
        $newUser->discord_handle = $user->nickname;
        $newUser->discord_id     = $user->id;
        $newUser->save();

        Auth::login($newUser, true);
        //Will utlimately redirect to profile but with terms and account name requirement message
        return redirect(route('dashboard'));
    }
}
