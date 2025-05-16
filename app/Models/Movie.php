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
    ];

    public function genres(){
     return $this->belongsToMany(Genre::class, 'genre_movie');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'user_movie');
    }

}
