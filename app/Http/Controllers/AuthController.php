<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @return mixed
     */
    public function checkMovies(): mixed
    {
        $moviesController = new MediasController();
        if (count($moviesController->showMoviesByUser()->getData()) >0) {
           return response()->json(['code'=> 200,'message' => "Des films sont enregistres"]);
        } else {
            return response()->json(['code'=> 400,'message' => "Aucun film n'est enregistre"]);
        }
    }

    public function checkSeries(): mixed
    {
        $seriesController = new MediasController();
        if (count($seriesController->showSeries()->getData()) >0) {
           return response()->json(['code'=> 200,'message' => "Des series sont enregistres"]);
        } else {
            return response()->json(['code'=> 400,'message' => "Aucune serie n'est enregistre"]);
        }
    }

    public function checkEmail(Request $request): mixed
    {
        $validated = $request->validate([
            'email' => 'required', 'string',
        ]);
        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            return response()->json(['code'=> 200, 'message' => "l'adresse e-mail est presente en base"]);
        } else {
            return response()->json(['code'=> 400,'message' => "Aucun compte n'est associé a cette adresse"]);
        }
    }

    public function checkUser(): mixed
    {
        if (Auth::check()) {
            return response()->json(['code'=> 200,'message' => "L'utilisateur est authentifié"]);
        } else {
            return response()->json(['code'=> 400,'message' => "L'utilisateur n'est pas authentifié"]);
        }
    }
    public function forgotPasswordUser(Request $request): mixed
    {
        $validated = $request->validate([
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);
        $user = User::where('email', $validated['email'])->first();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json(['code'=> 200,'message' => "Votre mot de passe a été réinitalisée"]);

    }

}
