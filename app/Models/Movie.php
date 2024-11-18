<?php

namespace App\Models;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movie extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_movie",
        "name",
        "synopsis",
        "url_img"
        "url_img"
    ];

    public function genre(){
     return $this->belongsToMany(Genre::class, 'genre_movie');
    }

}
