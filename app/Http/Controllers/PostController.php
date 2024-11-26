<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostReqest;

class PostController extends Controller
{
    public function createPost(PostReqest $request){
        if($request->validated()){
            $post = Post::create([
                "note" => $request->note,
                "comment" => $request->comment
            ]);
            $post->movie()->associate($request->movie_id);
            $post->save();
            return ["code"=> 200, "message"=> "Toutes les données ont été validées"];
        } else {
            return ["code"=> 500, "message"=> "Une ou plusieurs données ne sont pas validés"];
        }
    }
}
