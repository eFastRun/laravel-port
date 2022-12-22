<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\auth\UserVerify;
use App\Mail\SendMail;
use App\Mail\VerifySuccess;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Exception;

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
                    return response(["success" => false, "message" => 'Error caused when sending email verification code'], '400');
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
            Mail::to($request->email)->send(new SendMail($token));
        } catch(\Exception $ex) {
            return 'email failed to be sent';
        }

        return 'success';
    }

    /**
     * Send Email verification again
     * 
     */
    public function sendEmailAgain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);

        if ($validator->passes()) {
            $token = $this->generate_string(6);

            $user = User::where('email', $request->email)->get()->first();
    
            UserVerify::create([
                'user_id' => $user->id,
                'token' => $token
            ]);

            try{
                Mail::to($request->email)->send(new SendMail($token));
            } catch(\Exception $ex) {
                return response(['success' => false, 'message' => 'email failed to be sent'], '400');
            }

            return response(['success' => true, 'message' => 'Resent Email Verification Code Successfully'], '200');
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    /**
     * Verify user account
     *
     * @return response()
     */
    public function verifyAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:6',
        ]);

        if ($validator->passes()) {
            $token = $request->token;

            $verifyUser = UserVerify::where('token', $token)->first();

            if(!is_null($verifyUser) ){
                $user = $verifyUser->user;

                if(!$user->is_email_verified) {
                    $verifyUser->user->is_email_verified = 1;
                    $verifyUser->user->save();

                    try{
                        Mail::to($verifyUser->user->email)->send(new VerifySuccess());
                    } catch(\Exception $ex) {
                        return response(['success' => false, 'message' => 'email failed to be sent'], '400');
                    }

                    return response(['success' => false, 'message' => 'Successfully Verified Your Email.'], '200');
                } else {
                    return response(['success' => false, 'message' => 'Sorry your email cannot be identified. You already verified your email.'], '400');
                }
            }

            return response(['success' => false, 'message' => 'Sorry your email cannot be identified. Your email not exists.'], '400');
        }
        
        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    /**
     * Legal name
     *
     * @return response()
     */
    public function updateLegalName(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'firstName' => 'required|string|min:2',
            'lastName' => 'required|string|min:2',
        ]);

        if ($validator->passes()) {
            $user = User::where('email', $request->email)->get()->first();

            if(!is_null($user)){
                if ($user->is_email_verified) {
                    $user->firstName = $request->firstName;
                    $user->lastName = $request->lastName;

                    if($user->save()){
                        return response(['success' => true, 'message' => 'Stored User Information Successfully.'], '200');
                    } else {
                        return response(['success' => false, 'message' => 'An error caused when storing data.'], '401');
                    }
                }

                return response(['success' => false, 'message' => 'Please verify your email first.'], '403');
            }

            return response(['success' => false, 'message' => 'Your email not exists.'], '400');
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }
}