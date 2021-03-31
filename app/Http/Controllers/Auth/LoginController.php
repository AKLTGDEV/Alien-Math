<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\KeysModel;
use App\utils\publicws;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Socialite;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the provider authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }


    /**
     * Obtain the user information from Provider.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($driver)
    {
        $user = Socialite::driver($driver)->user();

        /*
         * We have the basic info about the user, and we are sure that
         * the email belongs to him.
         *
         * If the email doesn't exist in the records, create a new user
         * with a random password. Else, just log him in.
         */

        if ($driver == "google") {
            $user_instance = User::where("email", $user->email)->first();
            if ($user_instance != null) {
                // The user exists. Log him in.
            } else {
                $user_instance = new User;
                $user_instance->provider_name = "google";
                $user_instance->provider_id = $user->id;
                $user_instance->email = $user->email;
                $user_instance->name = $user->name;
                //$user_instance->username = $user->email;
                $user_instance->password = bcrypt("1234" . rand(0, 100) . rand(0, 100) . $user->email);
                $user_instance->avatar = $user->avatar;
                $user_instance->email_verified_at = Carbon::now()->toDateTimeString();
                $user_instance->save();
            }

            //Check if the user is coming here right after attempting a public worksheet
            if (Session::has('PUBLIC_WSATT_SLUG') && Session::has('PUBLIC_WSATT_PID')) {
                $ws_slug = Session::get('PUBLIC_WSATT_SLUG');
                $pid = Session::get('PUBLIC_WSATT_PID');

                publicws::save_progress($user_instance, $ws_slug, $pid);
            }

            Auth::login($user_instance);
            return redirect(route('home'));
        }
    }

    public function api_login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $key = $user->generateToken();
            $data = $user->toArray();
            //$data['key'] = $key;

            return response()->json([
                'data' => $data,
            ]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function api_logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            KeysModel::where("apikey", $user->api_token)->delete();

            $user->api_token = null;
            $user->save();
        }

        return response()->json(['data' => 'User logged out.'], 200);
    }
}
