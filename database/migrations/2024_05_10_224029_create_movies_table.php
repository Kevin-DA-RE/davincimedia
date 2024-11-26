<?php

use App\Models\Movie;
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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_movie');
            $table->string('name');
            $table->string('synopsis');
            $table->string('url_img');
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table){
            $table->foreignIdFor(Movie::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
        Schema::table('posts', function (Blueprint $table){
            $table->dropConstrainedForeignIdFor(Movie::class);
        });
    }
};
