<?php

use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer("note");
            $table->string("comment");
            $table->timestamps();
        });

        Schema::table('movies', function (Blueprint $table){
            $table->foreignIdFor(Post::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::table('posts', function (Blueprint $table){
            $table->dropConstrainedForeignIdFor(Post::class);
        });
    }
};
