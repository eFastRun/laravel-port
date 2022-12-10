<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\IntegratorController;
use App\Http\Controllers\UserController;
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
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('user.logout');
    
    Route::prefix('/user')->group(function () {
        Route::get('/current', [UserController::class, "currentUser"])->name('user.current');

        // user management
        Route::get("/list", [UserController::class, "index"])->name('user.list');
        Route::post("/update", [UserController::class, "updateUser"])->name('user.update');
        Route::delete("/delete", [UserController::class, "deleteUser"])->name('user.delete');
    });

    Route::prefix('/integrator')->group(function () {
        Route::post('/update-webhook', [IntegratorController::class, "updateIntegratorWebhook"])->name("integrator.update.webhook");
        Route::post('/deposit', [IntegratorController::class, "updateIntegratorDeposit"])->name("integrator.deposit");
        Route::get('/ledger-balance', [IntegratorController::class, "getLedgerBalance"])->name("ledger.get.balance");
        Route::post('/create-ledger', [IntegratorController::class, "createFloatLedger"])->name("ledger.create");
    });
});