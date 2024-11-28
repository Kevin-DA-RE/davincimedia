<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return view('app');
});


Route::controller(MoviesController::class)->group(function () {
    Route::post('/movie/create-movie', 'createMovie');
    Route::post('/movie/update-movie', 'updateMovie');
});

Route::controller(PostController::class)->group(function () {
    Route::post('/post/create-post', 'createPost');
    Route::post('/post/update-post', 'updatePost');
});

