<?php

use App\Http\Controllers\MentorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/mentors', [MentorController::class, 'store']);
Route::put('/mentors/{mentor}', [MentorController::class, 'update']);
Route::get('/mentors/{id}', [MentorController::class, 'show']);
Route::get('/mentors', [MentorController::class, 'index']);
Route::delete('/mentors/{id}', [MentorController::class, 'destroy']);
