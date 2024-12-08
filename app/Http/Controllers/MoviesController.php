<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieListRequest;
use App\Http\Requests\MovieRequest;

class MoviesController extends Controller
{
    public function showMovie(){
        return response()->json(Movie::all());
    }
    public function showGenre(){
        return response()->json(Genre::all());
    }
    public function createMovie (MovieListRequest $request)
    {
        $item = $request->validated();

            foreach ($item->moviesList as $request_movie) {
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
      }

    public function updateMovie (MovieRequest $request, Movie $id)
    {
        $item = $request->validated();
        $genre_ids =[];
        $movie = $id;
        $movie->name = $item->name;
        $movie->synopsis = $item->synopsis;
        $movie->url_img = $item->url_img;
        foreach ($item->genre as $movie_genre) {
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

    }
}
