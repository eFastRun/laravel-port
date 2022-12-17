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
    }

    public function userLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:3|max:255|exists:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->passes()) {
            if (auth()->check() == true) {
                return response(["success" => false, "errors" => "You already joined"], '401');
            }

            $status = auth()->attempt(['email' => $request->email, 'password' => $request->password]);

            if ($status) {
                if (auth()->user()->is_active == 0) {
                    return response(["success" => false, "message" => "Your account is restricted. Contact with our support team."], '401');
                }

                if (auth()->user()->is_email_verified == 0) {
                    return response(["success" => false, "message" => "Verify your email first."], '401');
                }

                return response(["success" => true, 'message' => 'Successfully signed in', "user" => auth()->user()], '200');
            }

            return response(["success" => false, "message" => "User or password invalid"], '403');
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
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