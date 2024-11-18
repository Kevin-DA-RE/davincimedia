<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieRequest;

class MoviesController extends Controller
{
    public function create (MovieRequest $request)
    {
    foreach ($request->moviesList as $request_movie) {
            $genre_ids = [];
            foreach ($request_movie['genre'] as $request_genre) {
                    $genre = Genre::firstOrCreate(
                        ["id_genre" => $request_genre["id_genre"]],
                        [
                            "id_genre" => $request_genre["id_genre"],
                            "name" => $request_genre["name"]
                        ]
                    );

                    $genre_ids[] = $genre->id_genre;
                }

            $movie = Movie::firstOrCreate(
                ["id_movie" => $request_movie["id_movie"]],
                [
                    "id_movie" => $request_movie["id_movie"],
                    "name" => $request_movie["name"],
                    "synopsis" => $request_movie["synopsis"],
                    "url_img" => $request_movie["url_img"]
                ]

            );
            $movie->genre()->syncWithoutDetaching($genre_ids);
        }
    }

}
