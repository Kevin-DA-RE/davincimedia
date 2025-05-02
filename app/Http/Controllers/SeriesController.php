<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TMDB;
use App\Http\Resources\SeriesResources;
use App\Http\Resources\GenresResources;
use App\Models\Serie;
use App\Models\Genre;
use App\Http\Requests\SerieListRequest;
use App\Http\Requests\SerieRequest;

class SeriesController extends Controller
{
    public function getSerie(string $query, TMDB $tmdb)
    {
        $request = $tmdb->getUrlTMDB('tv', $query);
        $serieList = $request['results'];
        if(array_key_exists(0, $serieList)){
            $urlPictureComplete = "https://image.tmdb.org/t/p/w500".$serieList[0]['poster_path'];
            $serieList[0]['poster_path'] = $urlPictureComplete;
            $movie = new SeriesResources($serieList[0]);
        return response()->json($movie->getSerie());
        } else{
            $statusCode = $request ? $request->status() : 500;
            return response()->json(["code" => $statusCode, "message" => "Aucun film trouvé"]);
        }
    }

    public function getSerieWithGenres(string $name, TMDB $tmdb)
    {
        $serie = $this->getSerie($name, $tmdb)->getData();
        if(property_exists($serie, "id")){
            $genresList = $tmdb->getGenres("serie")->getData();
            $genresSerie = $serie->genre_ids;
            foreach ($genresList as $valueOrigin) {
                foreach ($genresSerie as $valueMovie) {
                    if ($valueOrigin->id === $valueMovie) {
                        $serie->genres[] = [
                            "id_genre" => $valueOrigin->id,
                            "name" => $valueOrigin->name
                        ];
                    }
                }
            }
            $serie = new SeriesResources($serie);
            return response()->json($serie->getSerieWithGenres());
        } else {
            return response()->json([
                "code" => 400,
                "message" => "Aucun film trouvé"
            ]);
        }
    }


    public function showSerie(){
        $movies = Serie::with('genre')->get();
        $movies = SeriesResources::collection($movies);
        return response()->json($movies);
    }


    public function showGenres(){
        $genres = Genre::whereHas('serie')->get();
        $genres = GenresResources::collection($genres);
        return response()->json($genres);
    }

    public function showSerieWithGenres(Genre $genre){
        $moviesWithGenres = SeriesResources::collection($genre->movie);
        return response()->json($moviesWithGenres);
    }

    public function createSerie (SerieListRequest $request)
    {
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
                $movie = Serie::firstOrCreate(
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

    public function updateSerie (SerieRequest $request, Serie $movie)
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

    public function deleteSerie (Request $request, Serie $movie)
    {
        $item = $request->validate([
            'id_movie' => ['required', 'integer'],
            "genres.*.id_genre" =>  ['required', 'integer'],
            "genres.*.name" => ['required', 'string']
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
