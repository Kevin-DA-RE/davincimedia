<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadMovieRequest;
use App\Models\Movie;
use Illuminate\Http\UploadedFile;

class MoviesController extends Controller
{
    public function getMovie ()
    {
        $result = Movie::all();
    }

    public function uploadMovie (UploadMovieRequest $request)
    {
        /** @var UploadedFile $image */

        // Nous checkons si le champs envoyé depuis le back correspond au champs attendu avec les restrictions enregistrées.
        $image = $request->validated('video');

        // Nous enregistrons l'image dans dans le dossier video_download du dossier public et le chemin de ce dossier est enregistré dans une variable
        $imagePath = $image->store('video_download', 'public');
        return ["code" => 200, "message" => "L'image a bien été uploadé dans dossier ". $imagePath ." !"];

    }


}
