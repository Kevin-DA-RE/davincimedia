<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required', 'string',
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
            ]);
            return response()->json(['message' => "l'utilisateur ".$validated['name']." a bien été crée"],200);
        } else {
            return response()->json(['message' => "l'adresse e-mail est déja utilisé"],400);
        }
    }


    public function loginUser(Request $request){
        $validated = $request->validate([
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);
        $user = User::where('email', $validated['email'])->first();
        if ($user) {
            if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
                return $user->createToken('authToken')->plainTextToken;
            } else {
                return response()->json(['message' => "le mot de passe est incorrect"],400);
            }
        } else {
            return response()->json(['message' => "l'adresse e-mail n'existe pas"],400);
        }
    }

    public function logoutUser(Request $request){
        $user = $request->user();
        $user->tokens()->delete(); // Supprimer tous les tokens de l'utilisateur
        Auth::logout();
        return response()->json(['message' => "vous avez bien été déconnecté"],200);
    }

}
