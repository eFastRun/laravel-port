<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
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

Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::get('/current', [LoginController::class, "currentUser"])->name('user.current');

Route::post('/logout', [LoginController::class, 'logout'])->name('user.logout');
Route::post('/login', [LoginController::class, 'userLogin'])->name('login.post');