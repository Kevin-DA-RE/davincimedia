<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoviesResources extends JsonResource
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
        'name' => $this->name,
        'synopsis' => $this->synopsis,
        'url_img' => $this->url_img
       ];
    }


    public function getMovieWithGenres()
    {
       return [
        'id_movie' => $this->id,
        'name' => $this->title,
        'synopsis' => $this->overview,
        'url_img' => $this->poster_path,
        'genres' =>  $this->genres
       ];
    }

}
