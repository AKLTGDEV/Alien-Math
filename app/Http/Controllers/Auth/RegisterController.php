<?php

namespace App\Http\Controllers\Auth;

use App\activitylog;
use App\User;
use App\Http\Controllers\Controller;
use App\KeysModel;
use App\rating;
use App\UserModel;
use App\utils\publicws;
use App\WorksheetModel;
use App\wsAttemptsModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use TeamTNT\TNTSearch\TNTSearch;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'pname' => 'required|string|max:255',
            //'username' => 'required|string|max:25|unique:users',
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        Storage::put("answers/" . $data['username'], "[]");

        /**
         * 
         * Data is not getting saved for some reason. Come back later
         * FIXME TODO
         * 
         */

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'type' => "student",
            'gender' => $data['gender'],
            'grade' => $data['grade'],
            'level' => $data['level'],
            'parent_name' => $data['pname'],
            'contact' => $data['contact'],
        ]);

        $tnt = new TNTSearch;
        $tnt->loadConfig([
            'driver'    => 'mysql',
            'host'      => config("DB_HOST"),
            'database'  => config("DB_DATABASE"),
            'username'  => config("DB_USERNAME"),
            'password'  => config("DB_PASSWORD"),
            'storage'   => storage_path('app') . "/indices//",
        ]);
        $tnt->selectIndex("users.index");
        $index = $tnt->getIndex();

        $index->insert([
            'id' => $user->id,
            'username' => $user->username,
        ]); // Not Now..

        //Check if the user is coming here right after attempting a public worksheet
        if (Session::has('PUBLIC_WSATT_SLUG') && Session::has('PUBLIC_WSATT_PID')) {
            $ws_slug = Session::get('PUBLIC_WSATT_SLUG');
            $pid = Session::get('PUBLIC_WSATT_PID');

            publicws::save_progress($user, $ws_slug, $pid);
        }

        return $user;
    }

    public function api_register(Request $request)
    {
        // Here the request is validated. The validator method is located
        // inside the RegisterController, and makes sure the name, email
        // password and password_confirmation fields are required.
        $this->validator($request->all())->validate();

        // A Registered event is created and will trigger any relevant
        // observers, such as sending a confirmation email or any 
        // code that needs to be run as soon as the user is created.

        //event(new Registered($user = $this->create($request->all())));
        $user = $this->create($request->all());

        // After the user is created, he's logged in.
        $this->guard()->login($user);

        // And finally this is the hook that we want. If there is no
        // registered() method or it returns null, redirect him to
        // some other URL. In our case, we just need to implement
        // that method to return the correct response.
        return $this->api_registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function api_registered(Request $request, $user)
    {
        $key = $user->generateToken();
        $data = $user->toArray();
        $data['nos_following'] = 0;
        $data['nos_followers'] = 0;
        $data['rating'] = 0;
        return response()->json(['data' => $data], 201);
    }
}
