<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieRequest;

class MoviesController extends Controller
{
    public function create (MovieRequest $request)
    {
        foreach ($request->moviesList as $movie) {
            foreach ($movie['genre'] as $genre) {
                $genre_id = $genre["id"];
                Genre::firstOrCreate(
                    ["id_genre" => $genre["id"]],
                    ["name" => $genre["name"]]
                );
            }

            Movie::firstOrCreate(
                ["id_movie" => $movie["id"]],
                ["name" => $movie["name"]],
                ["synopsis" => $movie["synopsis"]],
                ["url_img" => $movie["url_img"]],
                ["genre_id" => $genre_id]
            );
        }





    }

}
