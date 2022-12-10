<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles user management
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(5);

        return response($users, 200);
    }

    /**
     * Get currently logged user.
     *
     * @return
     */
    public function currentUser(Request $request) {
        return response(["user" => auth()->user()], 200);
    }

    /**
     * Update user.
     *
     * @return
     */
    public function updateUser(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            "email" => "required|email|exists:users,email"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator->errors()
            ], 422);
        }

        $vals = array("email" => $data['email']);

        $fullName = isset($data['fullName']) ? $data['fullName'] : NULL;
        $country = isset($data['country']) ? $data['country'] : NULL;
        $contactNumber = isset($data['contactNumber']) ? $data['contactNumber'] : NULL;

        if ($fullName != NULL)
            $vals['fullName'] = $fullName;
        if ($country != NULL)
            $vals['country'] = $country;
        if ($contactNumber != NULL)
            $vals['contactNumber'] = $contactNumber;

        $updated = User::where('email', $data['email'])->update($vals);

        return response(["success" => true, "user" => $updated], 200);
    }

    /**
     * Delete a user.
     *
     * @return
     */
    public function deleteUser(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            "email" => "required|email|exists:users,email"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "error" => $validator->errors()
            ], 422);
        }

        $deleted = User::where('email', $data['email'])->delete();

        return response(["success" => true, "user" => $deleted], 200);
    }
}