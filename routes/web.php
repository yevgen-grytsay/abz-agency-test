<?php

use App\Http\Controllers\Web\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', [UsersController::class, 'index']);

Route::get('/users/add', function() {
    return view('add_user');
});

Route::get('/users/{id}', [UsersController::class, 'show'])
    ->name('users.profile');
