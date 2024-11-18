<?php

namespace App\Models;

use App\Models\Movie;
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

    public function movie(){
        return $this->belongsToMany(Movie::class, 'genre_movie');
    }
}
