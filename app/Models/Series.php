<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_movie",
        "name",
        "synopsis",
        "url_img"
    ];

    public function genre(){
     return $this->belongsToMany(Genre::class, 'genre_serie');
    }

    public function post(){
        return $this->hasMany(Post::class);
    }

}
