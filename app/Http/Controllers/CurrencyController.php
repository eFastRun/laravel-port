<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Currency;

class CurrencyController extends Controller
{
    //
    public function __construct() {
    }
    
    public function addNewCurrency (Request $request) {
        $validator = Validator::make($request->all(), [
            'symbol' => 'required|string',
            'name' => 'required|string',
            'type' => 'required|in:1,2',
            'api_id' => 'nullable|sometimes|number|unique:currency',
        ]);

        if ($validator->passes()) {
            $currency = Currency::create([
                'symbol' => $request->symbol,
                'name' => $request->symbol,
                'type' => $request->type
            ]);

            if ($request->type == 1) {
                $currency->api_id = $request->api_id;
            }
            
            if ($currency->save()) {
                return response(["success" => true, 'message' => 'Currency Added', "currency" => $currency], '200');
            }
            return response(["success" => false, 'message' => 'An error occured during saving data'], '400');
        }

        return response(['success' => false, 'message' => 'Validation Error', 'errors' => $validator->errors()], '400');
    }

    public function getCurrencies (Request $request) {
        $currencies = Currency::get();

        return response(['success' => true, 'message' => 'Here are currencies', 'currencies' => $currencies], 200);
    }

    public function getCryptoCurrencies (Request $request) {
        $currencies = Currency::where(
            'type', 1
        )->get();

        return response(['success' => true, 'message' => 'Here are currencies', 'currencies' => $currencies], 200);
    }

    public function getFiatCurrencies (Request $request) {
        $currencies = Currency::where(
            'type', 2
        )->get();

        return response(['success' => true, 'message' => 'Here are currencies', 'currencies' => $currencies], 200);
    }
}