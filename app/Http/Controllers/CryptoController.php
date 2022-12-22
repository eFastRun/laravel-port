<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use App\Models\Currency;
use App\Models\Wallet;

class CryptoController extends Controller
{
    //
    public function __construct() {
    }

    public function exchangeCryptoToCrypto (Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|number',
            'from' => 'required|number|exists:currency',
            'to' => 'required|number|exists:currency',
            'amount' => 'required|number',
        ]);

        if ($validator->passes()) {
            $wallet = Wallet::where('user_id', $request->user_id)->get();
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    public function exchangeCryptoToFiat (Request $request) {
    }

    public function transferFunds (Request $request) {
    }
}