<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use App\Http\Requests\UploadMovieRequest;

class MoviesController extends Controller
{
    public function uploadMovie (UploadMovieRequest $request)
    {
         dd($request->validated());
        /** @var UploadedFile $image */
        /*
        $image = $request->validated('video');

        // Nous enregistrons l'image dans dans le dossier video_download du dossier public et le chemin de ce dossier est enregistré dans une variable
        $imagePath = $image->store('video_download', 'public');
        return ["code" => "200", "message" => "le(s) fichier(s) ont bien été uploadé sur le serveur \n"+ $imagePath + ''];
        */
    }
}
