<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediasController;

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

Route::middleware('auth:sanctum')->prefix('media/movie')->controller(MediasController::class)->group(function () {
    Route::post('/create-movies', 'createMovies');
    Route::post('/update-movie/{movie}', 'updateMovie');
    Route::post('/delete-movie/{movie}', 'deleteMovie');
    Route::get('/show-movies', 'showMovies');
    Route::get('/show-movies-genres', 'showMovieGenres');
    Route::get('/show-movies-genres/{genre}', 'showMoviesWithGenres');
    Route::get('/get-movie-with-genres/{name}', 'getMovieWithGenres');
});

Route::middleware('auth:sanctum')->prefix('media/serie')->controller(MediasController::class)->group(function () {
    Route::post('/create-series', 'createSeries');
    Route::post('/update-serie/{serie}', 'updateSerie');
    Route::post('/delete-serie/{serie}', 'deleteSerie');
    Route::get('/show-series', 'showSeries');
    Route::get('/show-genres-series', 'showGenresSeries');
    Route::get('/show-series-genres/{genre}', 'showSeriesWithGenres');
    Route::get('/get-serie-with-genres/{name}', 'getSerieWithGenres');
});

/*
Route::prefix('media/serie')->controller(MediasController::class)->group(function () {
    Route::get('/testSerie', 'showMovieGenres');
    Route::get('/testMovie/{query}', 'getMovie');
    //Route::post('/create-series', 'createSeries');
});
*/
