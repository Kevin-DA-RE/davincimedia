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
        'id_movie' => $this->id_movie,
        'name' => $this->name,
        'synopsis' => $this->synopsis,
        'url_img' => $this->url_img,
        'genres' => $this->genre->map(function ($genre) {
            return [
                'id_genre' => $genre->id,
                'name' => $genre->name
            ];
        }),
       ];
    }
}
