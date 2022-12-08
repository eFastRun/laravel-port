<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectAfterLogout = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', ['except' => 'userLogin']);
    }

    public function currentUser(Request $request) {
        return response()->json(["user" => auth()->user()]);
    }

    public function userLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|exists:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->passes()) {
            // return response(["authenticated" => auth()->check()]);

            if (auth()->check() == true) {
                return response(["success" => false, "errors" => "You already joined"]);
            }

            $user = auth()->attempt($request->only('email', 'password'));

            if ($user) {
                return response(["success" => true, "user" => auth()->user()]);
            }

            return response(["success" => false, "errors" => "User or password invalid"]);
        }

        return response(["success" => false, "errors" => $validator->errors()]);
    }

    /**
     * Logout, Clear Session, and Return.
     *
     */
    public function logout() {
        $user = auth()->user();

        auth()->logout();
        Session::flush();

        return response(["message" => "Successfully logged out", "user" => $user], 200);
    }
}