<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Hash;

class ForgetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Forget Controller
    |--------------------------------------------------------------------------
    |
    | Controller to handle forget password action
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    public function submitForgetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:3|max:255|exists:users',
        ]);

        if ($validator->passes()) {
            $token = Str::random(8);

            DB::table('password_resets')->insert([
                'email' => $request->email, 
                'token' => $token, 
                'created_at' => Carbon::now()
            ]);

            try{
                Mail::to($request->email)->send(new ForgetPassword($token));
                
                return response(["success" => true, 'message' => 'Verification Code Sent'], '200');
            } catch(\Exception $ex) {
                return response(['success' => false, 'message' => 'email failed to be sent'], '400');
            }
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    /**
     * Verify Email For Reset password
     *
     * @return response()
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:8',
        ]);

        if ($validator->passes()) {
            $token = $request->token;

            $forgetPasswordRequest = DB::table('password_resets')->where([
                'token' => $request->token
            ])
            ->first();

            if(!$forgetPasswordRequest){
                return response(['success' => false, 'message' => 'Wrong Verification Code.'], '401');
            }

            return response(['success' => true, 'message' => 'Email Verified.'], '200');
        }
        
        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    /**
     * Reset new Password
     *
     * @return response()
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6',
            'token' => 'required|string|size:8',
        ]);

        if ($validator->passes()) {
            $token = $request->token;

            $forgetPasswordRequest = DB::table('password_resets')->where([
                'token' => $request->token
            ])
            ->first();

            if(!$forgetPasswordRequest){
                return response(['success' => false, 'message' => 'Wrong Verification Code.'], '401');
            }

            $user = User::where('email', $request->email)
                      ->update(['password' => Hash::make($request->password)]);

            DB::table('password_resets')->where(['email'=> $request->email])->delete();

            return response(['success' => true, 'message' => 'Password Changed.'], '200');
        }
        
        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }
}