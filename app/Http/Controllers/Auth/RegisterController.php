<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use \GuzzleHttp\Client;

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
    protected $redirectTo = '/activate';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() { }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->passes()) {
            // Store your user in database
            $result = $this->create($request->all());
            if (isset($result['success']) && $result['success'] == true) {
                return response($result, $result['code']);
            } else {
                return response(array(
                    'success' => false,
                    'message' => 'An error occurred when storing user',
                    'code' => $result['code']
                ), $result['code']);
            }
        }

        return response(['errors' => $validator->errors()], '400');
    }

    protected function create(array $data)
    {
        $referalCode = isset($data['referalCode']) ? $data['referalCode'] : NULL;

        $vals = array(
            'fullName' => $data['firstName']." ".$data['lastName'],
            'email' => $data['email'],
        );

        $vals['password'] = Hash::make($data['password']);

        if ($referalCode != NULL)
            $vals['referalCode'] = $referalCode;

        try {
            $user = User::create($vals);
            
            return array(
                'success' => true,
                'user' => $user,
                'code' => 200
            );
        } catch (\Exception $e) {
            return array(
                'success' => false,
                'code' => $e->getResponse()->getStatusCode()
            );
        }
    }
}

/////////////////// Store user to OneLiquidity
// try {
//     $client = new Client();

//     $res = $client->request('POST', 'https://sandbox-api.oneliquidity.technology/integrator/v1/register', [
//         'body' => json_encode($vals),
//         'headers' => [
//             'accept' => 'application/json',
//             'content-type' => 'application/json',
//         ],
//     ]);

//     $result = json_decode($res->getBody()->getContents());

//     $vals['password'] = Hash::make($data['password']);

//     if ($result->message == 'Ok') {
//         $vals['integratorId'] = $result->data->integratorId;

//         $user = User::create($vals);

//         return array(
//             'success' => true,
//             'user' => $user,
//             'result' => $result,
//             'integratorId' => $result->data->integratorId,
//             'code' => 200
//         );
//     } else {
//         return array(
//             'success' => true,
//             'message' => 'IntegratorId does not exist',
//             'error' => $result,
//             'code' => 403
//         );
//     }
// } catch (\GuzzleHttp\Exception\ClientException $e) {
//     return array(
//         'success' => false,
//         'message' => 'GuzzleHttp request error',
//         'error' => json_decode($e->getResponse()->getBody()->getContents()),
//         'code' => $e->getResponse()->getStatusCode()
//     );
// }

/////////////////// get IntegratorId
// try {
//     $client = new Client();

//     $integratorId = $result['integratorId'];

//     $vals = array('email' => $result['user']['email']);

//     $verifyRes = $client->request('POST', 'https://sandbox-api.oneliquidity.technology/integrator/v1/verify', [
//         'body' => json_encode($vals),
//         'headers' => [
//             'accept' => 'application/json',
//             'authorization' => 'Bearer '.$integratorId,
//             'content-type' => 'application/json',
//         ],
//     ]);

//     $verifyResult = json_decode($verifyRes->getBody()->getContents());

//     if ($verifyResult->message == "Ok") {
//         event(new Registered($result['user']));
//         return response($result, $result['code']);
//     } else {
//         return response('Email Verification Failed', 403);
//     }
// } catch (\GuzzleHttp\Exception\ClientException $e) {
//     return array(
//         'success' => false,
//         'message' => 'GuzzleHttp request error',
//         'error' => json_decode($e->getResponse()->getBody()->getContents()),
//         'code' => $e->getResponse()->getStatusCode()
//     );
// }