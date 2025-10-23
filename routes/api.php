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

Route::prefix('user')->group(function () {
    Route::get('/check-user', [AuthController::class,'checkUser']);
    Route::get('/check-movies', [AuthController::class,'checkMovies']);
    Route::get('/check-series', [AuthController::class,'checkSeries']);
    Route::post('/check-email', [AuthController::class,'checkEmail']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('media')->group(function () {
        Route::get('/user_movie', [MediasController::class, 'userMovies']);
        Route::prefix('movie')->group(function () {
            Route::post('/create-movies', [MediasController::class, 'createMovies']);
            Route::post('/update-movie/{movie}', [MediasController::class, 'updateMovie']);
            Route::post('/delete-movie/{movie}', [MediasController::class, 'deleteMovie']);
            Route::get('/show-movies-by-user', [MediasController::class, 'showMoviesByUser']);
            Route::get('/show-movies-genres', [MediasController::class, 'showMovieGenres']);
            Route::get('/show-movies-genres/{genre}', [MediasController::class, 'showMoviesWithGenres']);
            Route::get('/get-movie-with-genres/{name}', [MediasController::class, 'getMovieWithGenres']);
        });

        Route::prefix('serie')->group(function () {
            Route::post('/create-series', [MediasController::class,'createSeries']);
            Route::post('/update-serie/{serie}', [MediasController::class,'updateSerie']);
            Route::post('/delete-serie/{serie}', [MediasController::class,'deleteSerie']);
            Route::get('/show-series-by-user', [MediasController::class,'showSeriesByUser']);
            Route::get('/show-genres-series', [MediasController::class,'showGenresSeries']);
            Route::get('/show-genres-series/{genre}', [MediasController::class,'showSeriesWithGenres']);
            Route::get('/get-serie-with-genres/{name}', [MediasController::class,'getSerieWithGenres']);
        });
    });
});


