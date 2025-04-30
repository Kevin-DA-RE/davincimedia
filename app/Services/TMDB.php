<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Http\Resources\GenresResources;

class TMDB
{
    public function getUrlTMDB(string $parameter, string $query)
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

    public function getGenres(string $parameter)
    {
        $request = Http::withOptions(['verify' => false])->withToken(config('services.tmdb.key'))->acceptJson();
        if ($parameter === "movie") {
            $request = $request->get(config('services.tmdb.url_genre_movie'));
        } else {
            $request = $request->get(config('services.tmdb.url_genre_serie'));
        }
        return response()->json(GenresResources::collection($request['genres']));
    }
}
