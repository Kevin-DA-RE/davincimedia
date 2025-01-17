<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieListRequest;
use App\Http\Requests\MovieRequest;
use App\Http\Resources\GenresResources;
use App\Http\Resources\MoviesResources;

class MoviesController extends Controller
{
    public function showMovie(){
        return  MoviesResources::collection(Movie::all());
    }
    public function showGenre(){
        return GenresResources::collection(Genre::all());
    }
    public function createMovie (MovieListRequest $request)
    {
        //return response()->json(["code"=> 200, "message"=> "on entre dans le controlleur"]);
        $item = $request->validated();
            foreach ($item["moviesList"] as $request_movie) {
                $genre_ids =[];
                foreach ($request_movie["genre"] as $request_genre) {
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
            return response()->json(["code"=> 200, "message" => "tous les films ont bien ete enregistrés"]);
      }

    public function updateMovie (MovieRequest $request, Movie $movie)
    {
        $item = $request->validated();
        $genre_ids =[];
        $movie = Movie::UpdateOrCreate(
            ["id_movie" => $item["id_movie"]],
            [
                "name" => $item["name"],
                "synopsis" => $item["synopsis"],
                "url_img" => $item["url_img"]
            ]
            );
            
        foreach ($item["genre"] as $movie_genre) {
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
        return response()->json(["code"=> 200, "message" => "le film a bien été modifié"]);
    }
}
