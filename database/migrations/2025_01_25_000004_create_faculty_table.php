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
        Schema::create('faculty', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization');
            $table->text('bio')->nullable();
            $table->string('experience')->nullable();
            $table->longText('image')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('expertise_areas')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty');
    }
};
