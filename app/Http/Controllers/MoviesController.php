<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\UploadMovieRequest;

class MoviesController extends Controller
{
    public function getMovie ()
    {
        return Movie::all();
    }
    public function getGenre ()
    {
        return Genre::all();
    }

    public function uploadMovie (UploadMovieRequest $request)
    {

        /** @var UploadedFile $image */
        /*
        $image = $request->validated('video');
        $imagePath = $image->store('video_download', 'public');
        return ["code" => "200", "message" => "le(s) fichier(s) ont bien été uploadé sur le serveur \n"+ $imagePath + ''];
        */
    }
}
