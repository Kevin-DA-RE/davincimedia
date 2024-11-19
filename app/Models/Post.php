<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        "note",
        "comment"
    ];

    public function movie(){
     return $this->belongsTo(Movie::class);
    }
}
