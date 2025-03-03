<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function testUser(Request $request, User $user)
    {
        $passwordRequest = $request->validate([
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);
        $user->where('email', $passwordRequest['email'])->first();

        if ($user) {
            if (Hash::check($passwordRequest['password'], $user->password)) {
                $user->password = Hash::make($passwordRequest['password']);
                return response()->json(['message' => "Le mot de passe a été modifié avec succès"], 200);
            } else {
                return response()->json(['message' => "Les mots de passe ne correspondent pas"], 400);
            }
        } else {
            return response()->json(['message' => "L'utilisateur n'existe pas"], 400);
        }
    }

    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required', 'string',
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);

        $user = User::firstOrCreate([
            'email' => $validated['email'],
        ], [
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
        ]);

        if ($user->id) {
            return response()->json(['message' => "l'utilisateur est deja inscrit sur le site"],226);
        } else {
            return response()->json(['message' => "L'utilisateur a été crée qvec succès"],201);
        }

    }


    public function loginUser(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user && Hash::check($validated['password'], $user->password)) {
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token, 'message' => "Authentification réussie"], 200);
        } else {
            return response()->json(['message' => "Les identifiants sont incorrects"], 400);
        }
    }

    public function forgotPassword(Request $request, User $user)
    {
        $passwordRequest = $request->validate([
            'email' => 'required', 'string',
            'password' => 'required', 'string',
        ]);
        $user->where('email', $passwordRequest['email'])->first();

        if ($user) {
            if (Hash::check($passwordRequest['password'], $user->password)) {
                $user->password = Hash::make($passwordRequest['password']);
                return response()->json(['message' => "Le mot de passe a été modifié avec succès"], 200);
            } else {
                return response()->json(['message' => "Les mots de passe ne correspondent pas"], 400);
            }
        } else {
            return response()->json(['message' => "L'utilisateur n'existe pas"], 400);
        }
    }

    public function logoutUser(Request $request){
        $user = $request->user();
        $user->tokens()->delete(); // Supprimer tous les tokens de l'utilisateur
        Auth::logout();
        return response()->json(['message' => "vous avez bien été déconnecté"],200);
    }

}
