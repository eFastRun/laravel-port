<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the 'web' middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});
Route::get('/csrf', function () {
    return view('csrf_token');
});

Route::group(['middleware' => ['guest']], function () {
    Route::prefix('/user')->group(function () {
        Route::post('/login', [LoginController::class, 'userLogin'])->name('login.post');
        Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

        Route::post('/verifyCode', [RegisterController::class, 'sendEmailAgain'])->name('request.verify.email');
        Route::post('/verify', [RegisterController::class, 'verifyAccount'])->name('verify.account');

        Route::post('/legalName', [RegisterController::class, 'updateLegalName'])->name('update.legalName');
    });

    Route::prefix('/password')->group(function () {
        Route::post('/forgotRequest', [ForgetPasswordController::class, 'submitForgetPassword'])->name('request.forgot.password');
        Route::post('/verifyCode', [ForgetPasswordController::class, 'verifyEmail'])->name('forgot.verify.email');
        Route::post('/resetPassword', [ForgetPasswordController::class, 'resetPassword'])->name('password.reset');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('user.logout');

    Route::prefix('/currency')->group(function () {
        Route::post('/addNewCurrency', [CurrencyController::class, 'addNewCurrency'])->name('currency.add.new');
    });
});