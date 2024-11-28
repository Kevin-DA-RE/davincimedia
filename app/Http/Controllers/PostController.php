<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{


    public function createPost(PostRequest $request){
        if($request->validated()){
            $post = Post::create([
                "note" => $request->note,
                "comment" => $request->comment
            ]);
            $post->movie()->associate($request->movie_id);
            return ["code"=> 200, "message"=> "Toutes les données ont été validées"];
        } else {
            return ["code"=> 500, "message"=> "Une ou plusieurs données ne sont pas validés"];
        }
    }

    public function updatePost(PostRequest $request){
        if($request->validated())
        {
            $post = Post::find($request->id);
            $post->note = $request->note;
            $post->comment = $request->comment;
            $post->movie()->associate($request->movie_id);
            $post->save();

            return ["code"=> 200, "message"=> "Toutes les données ont été modifiées"];

        }else {
            return ["code"=> 500, "message"=> "Le films n'a été mis à jour"];
        }
    }

    public function deletePost(PostRequest $request){
            $post = Post::find($request->id);
            $post->delete();

            return ["code"=> 200, "message"=> "Le post a été supprimé"];
    }
}

