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
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['read_time', 'author_name', 'author_image', 'author_role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('read_time')->nullable();
            $table->string('author_name')->nullable(); // Made nullable for reverse compatibility if needed, but original was required. Let's stick to simple reverse or just text.
            $table->longText('author_image')->nullable();
            $table->string('author_role')->nullable();
        });
    }
};
