<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\auth\UserVerify;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Auth\Events\Registered;

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

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Returning Random string with specified strength.
     *
     * @param  integer strength
     * @return string
     */
    public function generate_string($strength = 16) {
        $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    /**
     * Register new user
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->passes()) {
            // Store your user in database
            $user=new User();
            $user->country = $request->input('country');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));

            if($user->save()){
               $sendResult = $this->sendEmailVerification($request,$user);

                if ($sendResult != 'success') {
                    return response(["success" => false, "message" => 'Error caused when sending email verification code'], '200');
                }
            }

            return response(["success" => true, 'message' => 'User registered successfully', "user" => $user], '200');
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    /**
     * Send Email Verification code to user's email
     * 
     * @param mixed $request
     * @param mixed $user
     * @return string
     */
    public function sendEmailVerification($request, $user){
        $token = $this->generate_string(6);

        UserVerify::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        try{
            Mail::send('website.auth.email.emailVerificationEmail', ['token' => $token], function($message) use($request){
                $message->from('us@example.com', 'Laravel');

                $message->to($request->email);

                $message->subject('Email Verification Mail');
            });
        } catch(Exception $ex) {
            dd($ex->getResponse());

            return 'email failed to be sent';
        }

        return 'success';
    }
}