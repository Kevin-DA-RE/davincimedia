<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediasResources extends JsonResource
{
  /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
       return [
        'id' => $this->id,
        'id_movie' => $this->id_movie,
        'name' => $this->name,
        'synopsis' => $this->synopsis,
        'url_img' => $this->url_img,
        'genres' =>  $this->genres->map(function($genre) {
            return [
               'id' => $genre->id,
                'id_genre' => $genre->id_genre,
                'name' => $genre->name
            ];
        })
       ];
    }

    public function getMovie()
    {
       return [
        'id' => $this['id'],
        'name' => $this['title'],
        'synopsis' => $this['overview'],
        'url_img' => $this['poster_path'],
        'genre_ids' => $this['genre_ids']
       ];
    }

    public function getSeries()
    {
       return [
        'id' => $this['id'],
        'name' => $this['name'],
        'synopsis' => $this['overview'],
        'url_img' => $this['poster_path'],
        'genre_ids' => $this['genre_ids']
       ];
    }

    public function getMovieWithGenres()
    {
       return [
        'id_movie' => $this->id,
        'name' => $this->name,
        'synopsis' => $this->synopsis,
        'url_img' => $this->url_img,
        'genres' => collect($this->genres)->map(function($genre) {
            return [
                'id_genre' => $genre['id_genre'],
                'name' => $genre['name']
            ];
        })
       ];
    }

    public function getSerieWithGenres()
    {
       return [
        'id_serie' => $this->id,
        'name' => $this->name,
        'synopsis' => $this->synopsis,
        'url_img' => $this->url_img,
        'genres' =>  collect($this->genres)->map(function($genre) {
            return [
                'id_genre' => $genre['id_genre'],
                'name' => $genre['name']
            ];
        })
       ];
    }

}
