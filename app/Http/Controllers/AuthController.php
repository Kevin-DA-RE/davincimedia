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

}
