<?php

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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt');
            $table->longText('content');
            $table->longText('image')->nullable();
            $table->string('category');
            $table->string('read_time')->nullable();
            $table->date('published_at')->nullable();
            
            // Author details
            $table->string('author_name');
            $table->longText('author_image')->nullable();
            $table->string('author_role')->nullable();
            
            $table->json('tags')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
