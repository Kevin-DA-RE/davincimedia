<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadMovieRequest;
use App\Models\Movie;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class MoviesController extends Controller
{
    public function getMovie ()
    {
        $result = Movie::all();
    }

    public function uploadMovie (UploadMovieRequest $request)
    {
        /** @var UploadedFile $image */
        $image = $request->validated('video');
        $imagePath = $image->store('video_download', 'public');
        return ["code" => "200", "message" => "le(s) fichier(s) ont bien été uploadé sur le serveur \n"+ $imagePath + ''];
    }


}
