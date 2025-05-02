<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;
    protected $fillable = [
        "id_serie",
        "name",
        "synopsis",
        "url_img"
    ];

    public function genre(){
     return $this->belongsToMany(Genre::class, 'genre_serie');
    }
}
