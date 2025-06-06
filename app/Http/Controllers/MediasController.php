<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Http\Requests\MovieListRequest;
use App\Http\Requests\MovieRequest;
use App\Http\Requests\SerieRequest;
use App\Http\Requests\SerieListRequest;
use App\Http\Resources\GenresResources;
use App\Http\Resources\MediasResources;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class MediasController extends Controller
{
    /**
     * @return mixed
     */
    public function userMovies(): mixed
    {
        $movies = Movie::with(['genres', 'users'])->get();
        return response()->json($movies);

    }

    /**
     * @param string $query
     * @return mixed
     */
    public function getMovie(string $query): mixed
    {
        $request = $this->getUrlTMDB('movie',$query);

        $moviesList = $request['results'];
        if(array_key_exists(0, $moviesList)){
            $urlPictureComplete = "https://image.tmdb.org/t/p/w500".$moviesList[0]['poster_path'];
            $moviesList[0]['poster_path'] = $urlPictureComplete;
            $movie = new MediasResources($moviesList[0]);
        return response()->json($movie->getMovie());
        } else{
        $statusCode = $request ? $request->status() : 500;
        return response()->json(["code" => $statusCode, "message" => "Aucun film trouvé"]);
        }
    }


    /**
     * @param string $query
     * @return mixed
     */
    public function getSerie(string $query): mixed
    {
        $request = $this->getUrlTMDB('tv',$query);
        $serieList = $request['results'];
        if(array_key_exists(0, $serieList)){
            $urlPictureComplete = "https://image.tmdb.org/t/p/w500".$serieList[0]['poster_path'];
            $serieList[0]['poster_path'] = $urlPictureComplete;
            $serie = new MediasResources($serieList[0]);
        return response()->json($serie->getSeries());
        } else{
            $statusCode = $request ? $request->status() : 500;
            return response()->json(["code" => $statusCode, "message" => "Aucun film trouvé"]);
        }
    }

    /**
     * @param string $parameter
     * @param string $query
     * @return mixed
     */
    private function getUrlTMDB(string $parameter, string $query): mixed
    {
        $request = Http::withOptions(['verify' => false])->withQueryParameters([
            "query" => $query,
            "include_adult" => false,
            "language" => "fr-FR",
            "page" => 1
            ])->withToken(config('services.tmdb.key'))->acceptJson();

        if ($parameter === "movie") {
           return $request = $request->get(config('services.tmdb.url_movie'));
        } else {
           return $request = $request->get(config('services.tmdb.url_serie'));
        }

    }


    /**
     * @param string $parameter
     * @return mixed
     */
    public function getGenres(string $parameter): mixed
    {
        $request = Http::withOptions(['verify' => false])->withToken(config('services.tmdb.key'))->acceptJson();
        if ($parameter === "movie") {
            $request = $request->get(config('services.tmdb.url_genre_movie'));
        } else {
            $request = $request->get(config('services.tmdb.url_genre_serie'));
        }
        return response()->json(GenresResources::collection($request['genres']));
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getMovieWithGenres(string $name): mixed
    {
        $movie = $this->getMovie($name)->getData();
        if(property_exists($movie, "id")){
            $genresList = $this->getGenres("movie")->getData();
            $genresMovie = $movie->genre_ids;
            foreach ($genresList as $valueOrigin) {
                foreach ($genresMovie as $valueMovie) {
                    if ($valueOrigin->id === $valueMovie) {
                        $movie->genres[] = [
                            "id_genre" => $valueOrigin->id,
                            "name" => $valueOrigin->name
                        ];
                    }
                }
            }
            $movie = new MediasResources($movie);
            return response()->json($movie->getMovieWithGenres());
        } else {
            return response()->json([
                "code" => 400,
                "message" => "Aucun film trouvé"
            ]);
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getSerieWithGenres(string $name): mixed
    {
        $serie = $this->getSerie($name)->getData();
        if(property_exists($serie, "id")){
            $genresList = $this->getGenres("serie")->getData();
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
            $serie = new MediasResources($serie);
            return response()->json($serie->getSerieWithGenres());
        } else {
            return response()->json([
                "code" => 400,
                "message" => "Aucun film trouvé"
            ]);
        }
    }

    /**
     * @return mixed
     */
    public function showMoviesByUser(): mixed
    {
        $movies = MediasResources::collection(Movie::with('genres')
        ->whereHas('users', function (Builder $query) {
            $query->where('users.id', auth()->id());
        })
        ->get());
        return response()->json($movies);
    }

    public function showSeriesByUser(){
        $series = MediasResources::collection(Serie::with('genres')
        ->whereHas('users', function (Builder $query) {
            $query->where('users.id', auth()->id());
        })
        ->get());
        return response()->json($series);
    }

    public function showMovieGenres(){
        $genres = GenresResources::collection(Genre::whereHas('movies')->get());
        return response()->json($genres);
    }

    public function showGenresSeries(){
        $genres = GenresResources::collection(Genre::whereHas('series')->get());
        return response()->json($genres);
    }

    public function showMoviesWithGenres(Genre $genre){
        $moviesWithGenres = MediasResources::collection($genre->movies);
        return response()->json($moviesWithGenres);
    }

    public function showSeriesWithGenres(Genre $genre){
        $seriesWithGenres = MediasResources::collection($genre->series);
        return response()->json($seriesWithGenres);
    }

    public function createMovies (MovieListRequest $request)
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
            $movie = Movie::firstOrCreate(
                        ["id_movie" => $request_movie["id_movie"]],
                        [
                            "id_movie" => $request_movie["id_movie"],
                            "name" => $request_movie["name"],
                            "synopsis" => $request_movie["synopsis"],
                            "url_img" => $request_movie["url_img"]
                ]
            );

            $movie->users()->attach(auth()->id());
            $movie->genres()->attach($genre_ids);

        }
        return response()->json(["code"=> 200, "message" => "tous les films ont bien ete enregistrés"]);
      }

      public function createSeries (SerieListRequest $request)
      {
          $item = $request->validated();
              foreach ($item["seriesList"] as $request_serie) {
                  $genre_ids =[];

                  foreach ($request_serie["genres"] as $request_genre) {
                      $genre = Genre::firstOrCreate(
                          ["id_genre" => $request_genre["id_genre"]],
                          [
                              "id_genre" => $request_genre["id_genre"],
                              "name" => $request_genre["name"],
                          ]
                      );

                      array_push($genre_ids, $genre->id);
                  }
                  $serie = Serie::firstOrCreate(
                              ["id_serie" => $request_serie["id_serie"]],
                              [
                                  "id_serie" => $request_serie["id_serie"],
                                  "name" => $request_serie["name"],
                                  "synopsis" => $request_serie["synopsis"],
                                  "url_img" => $request_serie["url_img"]
                      ]
                  );

                $serie->users()->attach(auth()->id());
                $serie->genre()->attach($genre_ids);

              }
              return response()->json(["code"=> 200, "message" => "toutes les series ont bien ete enregistrés"]);
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

    public function updateSerie (SerieRequest $serieRequest, Serie $serie)
    {
        $item = $serieRequest->validated();
        $genre_ids =[];
        $serie->id_serie = $item["id_serie"];
        $serie->name = $item["name"];
        $serie->synopsis = $item["synopsis"];
        $serie->url_img = $item["url_img"];
        $serie->save();

        foreach ($item["genres"] as $serie_genre) {
            $genre = Genre::updateOrCreate(
                ['id_genre' => $serie_genre['id_genre']],
                [
                    'id_genre' => $serie_genre['id_genre'],
                    'name' => $serie_genre['name']
                ]
            );
            array_push($genre_ids, $genre->id);

        }
        $serie->genre()->sync($genre_ids);
        return response()->json(["code"=> 200, "message" => "la serie a bien été modifié"]);
    }

    public function deleteMovie (Request $request, Movie $movie)
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

    public function deleteSerie (Request $request, Serie $serie)
    {
        $item = $request->validate([
            'id_serie' => ['required', 'integer'],
            "genres.*.id_genre" =>  ['required', 'integer'],
            "genres.*.name" => ['required', 'string']
        ]);
        $genre_ids=[];

        foreach ($item["genres"] as $movie_genre) {
            array_push($genre_ids, $movie_genre['id_genre']);
        }
        $serie->genre()->detach($genre_ids);
        $serie->delete();
        return response()->json(["code"=> 200, "message" => "la serie a bien été supprimé"]);
    }

}
