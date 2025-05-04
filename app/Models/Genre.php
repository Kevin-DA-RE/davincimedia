<?php

namespace App\Models;

use App\Models\Movie;
use App\Models\Serie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "id_genre",
        "name"
    ];

    public function movies(){
        return $this->belongsToMany(Movie::class, 'genre_movie');
    }

    public function series(){
        return $this->belongsToMany(Serie::class, 'genre_serie');
    }
}
