<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;

class IntegratorController extends Controller
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
     * Update Integrator Webhook.
     *
     */
    public function updateIntegratorWebhook(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'webhook' =>'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'error' => $validator->errors()
            ],422);
        }

        try {
            $client = new Client();

            $vals = array('webhook' => $data['webhook']);

            $response = $client->request('PATCH', 'https://sandbox-api.oneliquidity.technology/integrator/v1/webhook', [
                'body' => json_encode($vals),
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.config('global.auth-one.token'),
                    'content-type' => 'application/json',
                ],
            ]);

            $result= json_decode($response->getBody()->getContents());

            if ($result->message == "Ok") {
                return response(["success" => true, "message" => "Ok"], 200);
            } else {
                return response(["success" => false, "message" => 'Error occured when api call'], 403);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response(array(
                'success' => false,
                'message' => 'GuzzleHttp request error',
                'error' => json_decode($e->getResponse()->getBody()->getContents()),
                'code' => $e->getResponse()->getStatusCode()
            ), $e->getResponse()->getStatusCode());
        }
    }

    /**
     * Get ledger balance.
     *
     */
    public function getLedgerBalance(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'currency' =>'required',
            'ledger' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'error' => $validator->errors()
            ],422);
        }

        try {
            $client = new Client();

            $response = $client->request('GET', 'https://sandbox-api.oneliquidity.technology/integrator/v1/float?currency='.$data['currency'].'&ledger='.$data['ledger'], [
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.config('global.auth-one.token'),
                ],
            ]);

            $result= json_decode($response->getBody()->getContents());

            if ($result->message == "Ok") {
                return response(["success" => true, "data" => $result->data], 200);
            } else {
                return response(["success" => false, "message" => 'Error occured when api call'], 403);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response(array(
                'success' => false,
                'message' => 'GuzzleHttp request error',
                'error' => json_decode($e->getResponse()->getBody()->getContents()),
                'code' => $e->getResponse()->getStatusCode()
            ), $e->getResponse()->getStatusCode());
        }
    }

    /**
     * Create float ledger.
     *
     */
    public function createFloatLedger(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'currency' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'error' => $validator->errors()
            ],422);
        }

        try {
            $client = new Client();

            $vals = array('currency' => $data['currency']);

            $response = $client->request('POST', 'https://sandbox-api.oneliquidity.technology/integrator/v1/float', [
                'body' => json_encode($vals),
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.config('global.auth-one.token'),
                    'content-type' => 'application/json',
                ],
            ]);

            $result= json_decode($response->getBody()->getContents());

            if ($result->message == "Ok") {
                return response(["success" => true, "message" => "Float account created"], 200);
            } else {
                return response(["success" => false, "message" => $result->message, "data" => $result], 400);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response(array(
                'success' => false,
                'message' => 'GuzzleHttp request error',
                'error' => json_decode($e->getResponse()->getBody()->getContents()),
                'code' => $e->getResponse()->getStatusCode()
            ), $e->getResponse()->getStatusCode());
        }
    }

    /**
     * Update Integrator Deposit.
     *
     */
    public function updateIntegratorDeposit(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'amount' =>'required',
            'currency' =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                'error' => $validator->errors()
            ],422);
        }

        try {
            $client = new Client();

            $vals = array('amount' => intval($data['amount']), 'currency' => $data['currency']);

            $response = $client->request('POST', 'https://sandbox-api.oneliquidity.technology/integrator/v1/deposit/float', [
                'body' => json_encode($vals),
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Bearer '.config('global.auth-one.token'),
                    'content-type' => 'application/json',
                ],
            ]);

            $result= json_decode($response->getBody()->getContents());

            if ($result->message == "Ok") {
                return response(["success" => true, "data" => $result], 200);
            } else {
                return response(["success" => false, "data" => $result], 400);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response(array(
                'success' => false,
                'message' => 'GuzzleHttp request error',
                'error' => json_decode($e->getResponse()->getBody()->getContents()),
                'code' => $e->getResponse()->getStatusCode()
            ), $e->getResponse()->getStatusCode());
        }
    }
}