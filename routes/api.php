<?php

use App\Http\Controllers\AuthController;
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

Route::get('/', function () {
    return view('app');
});


Route::prefix('/user')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'createUser');
    Route::get('/login', 'loginUsert');
    Route::post('/logout', 'logoutUser');
    Route::post('/forgot-password', 'forgotPasswordUser');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('movie')->controller(MoviesController::class)->group(function () {
    Route::post('/create-movie', 'createMovie');
    Route::post('/update-movie/{movie}', 'updateMovie');
    Route::post('/delete-movie/{movie}', 'deleteMovie');
    Route::get('/show-movies', 'showMovies');
    Route::get('/get-movie/{parameter}', 'getMovie');
    Route::get('/get-genres', 'getGenres');
    Route::get('/get-movies-with-genres/{name}', 'getMovieWithGenres');
    Route::get('/show-genres', 'showGenres');
    Route::get('/show-movies-genres/{genre}', 'showMoviesWithGenres');
    Route::get('/test', 'test');
});



Route::prefix('/post')->controller(PostController::class)->group(function () {
    Route::post('/create-post', 'createPost');
    Route::get('/show-post', 'showPost');
    Route::post('/update-post', 'updatePost');
    Route::post('/delete-post', 'deletePost');
});

