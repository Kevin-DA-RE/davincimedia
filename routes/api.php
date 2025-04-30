<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SeriesController;

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
require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('app');
});

Route::prefix('user')->controller(AuthController::class)->group(function () {
    Route::get('/check-user', 'checkUser');
    Route::get('/check-movies', 'checkMovies');
    Route::get('/check-series', 'checkSeries');
    Route::post('/check-email', 'checkEmail');
});

Route::middleware('auth:sanctum')->prefix('movie')->controller(MoviesController::class)->group(function () {
    Route::post('/create-movie', 'createMovie');
    Route::post('/update-movie/{movie}', 'updateMovie');
    Route::post('/delete-movie/{movie}', 'deleteMovie');
    Route::get('/show-movies', 'showMovies');
    Route::get('/get-movie/{query}', 'getMovie');
    Route::get('/get-genres/{param}', 'getGenres');
    Route::get('/get-movies-with-genres/{name}', 'getMovieWithGenres');

    Route::get('/show-genres', 'showGenres');
    Route::get('/show-movies-genres/{genre}', 'showMoviesWithGenres');
});

Route::controller(SeriesController::class)->group(function () {
    Route::get('/getSerie/{query}', 'getSerie');
    Route::get('/getMovie/{query}', 'getMovie');
    Route::get('/get-serie-with-genres/{name}', 'getSerieWithGenres');
});
