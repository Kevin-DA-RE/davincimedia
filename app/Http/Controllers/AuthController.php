<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        dd($request);
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
            return response()->json(['message' => "l'utilisateur ".$validated['name']],200);
        } else {
            return response()->json(['message' => "l'adresse e-mail est déja utilisé"],400);
        }
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
