<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        dd($request);
        return "Hello World";
    }
}
