<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

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
                $user->createToken('authToken')->plainTextToken;
                return response()->json(['message' => "l'utilisateur est connecté"],200);
            } else {
                return response()->json(['message' => "l'adresse e-mail ou le mot de passe est incorrect"],400);
            }
        } else {
            return response()->json(['message' => "l'adresse e-mail ou le mot de passe est incorrect"],400);
        }
    }

    public function logoutUser(Request $request){
        Auth::logout();
        return response()->json(['message' => "vous avez bien été déconnecté"],200);
    }

    public function checkEmail(Request $request)
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


}
