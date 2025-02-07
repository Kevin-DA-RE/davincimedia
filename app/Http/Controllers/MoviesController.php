<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieListRequest;
use App\Http\Requests\MovieRequest;
use App\Http\Resources\GenresResources;
use App\Http\Resources\MoviesResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MoviesController extends Controller
{

    public function test()
    {

      $request = Http::withQueryParameters([
        "query" => "uncharted",
        "include_adult" => false,
        "language" => "fr-FR",
        "page" => 1
      ])->withToken(config('services.tmdb.key'))->acceptJson()->get('https://api.themoviedb.org/3/search/movie');
      dd($request->json());
    }

    public function showMovies(){
        $movies = Movie::all();
        $movies = MoviesResources::collection($movies);
        return response()->json($movies);
    }

    public function showGenres(){
        $genres = Genre::whereHas('movie')->get();
        $genres = GenresResources::collection($genres);
        return response()->json($genres);
    }

    public function showMoviesWithGenres(Genre $genre){
        $moviesWithGenres = MoviesResources::collection($genre->movie);
        return response()->json($moviesWithGenres);
    }

    public function createMovie (MovieListRequest $request)
    {
        //return response()->json(["code"=> 200, "message"=> "on entre dans le controlleur"]);
        $item = $request->validated();
            foreach ($item["moviesList"] as $request_movie) {
                $genre_ids =[];

                foreach ($request_movie["genres"] as $request_genre) {
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
        $movie->id_movie = $item["id_movie"];
        $movie->name = $item["name"];
        $movie->synopsis = $item["synopsis"];
        $movie->url_img = $item["url_img"];
        $movie->save();

        foreach ($item["genres"] as $movie_genre) {
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

    public function deleteMovie (Request $request, Movie $movie)
    {
        $item = $request->validate([
            'id_movie' => 'required', 'integer',
            "genres.*.id_genre" =>  'required', 'integer',
            "genres.*.name" => 'required', 'string'
        ]);
        $genre_ids=[];

        foreach ($item["genres"] as $movie_genre) {
            array_push($genre_ids, $movie_genre['id_genre']);
        }
        $movie->genre()->detach($genre_ids);
        $movie->delete();
        return response()->json(["code"=> 200, "message" => "le film a bien été supprimé"]);
    }

}
