<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieListRequest;
use App\Http\Requests\MovieRequest;

class MoviesController extends Controller
{
    public function createMovie (MovieListRequest $request)
    {
        if($request->validated()){
            foreach ($request->moviesList as $request_movie) {
                $genre_ids =[];
                foreach ($request_movie['genre'] as $request_genre) {

                    $genre = Genre::firstOrCreate(
                        ["id_genre" => $request_genre["id_genre"]],
                        [
                            "id_genre" => $request_genre["id_genre"],
                            "name" => $request_genre["name"],
                        ]
                    );

                    array_push($genre_ids, $genre->id);
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
                $movie->genre()->attach($genre_ids);
            }

            return ["code"=> 200, "message"=> "Toutes les données ont été validées"];
        } else {
            return ["code"=> 500, "message"=> "Une ou plusieurs données ne sont pas validés"];
        }

    }

    public function updateMovie (MovieRequest $request)
    {
        if($request->validated())
        {
            $genre_ids =[];
            $movie = Movie::where('id_movie','=',$request->id_movie)->first();
            $movie->name = $request->name;
            $movie->synopsis = $request->synopsis;
            $movie->url_img = $request->url_img;
            foreach ($request->genre as $movie_genre) {
                $genre = Genre::updateOrCreate(
                    ['id_genre' => $movie_genre['id_genre']],
                    [
                        'id_genre' => $movie_genre['id_genre'],
                        'name' => $movie_genre['name']
                    ]
                );
                array_push($genre_ids, $genre->id);

            }
            $movie->genre()->sync($genre_ids);
            return ["code"=> 200, "message"=> "Toutes les données ont été modifiées"];

        }else {
            return ["code"=> 500, "message"=> "Le films n'a été mis à jour"];
        }
    }
}
