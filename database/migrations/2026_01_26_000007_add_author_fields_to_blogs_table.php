<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // Add only if missing (safe to run once; Laravel will error if already exists)
            if (!Schema::hasColumn('blogs', 'read_time')) {
                $table->string('read_time')->nullable()->after('category');
            }
            if (!Schema::hasColumn('blogs', 'author_name')) {
                $table->string('author_name')->default('Admin')->after('published_at');
            }
            if (!Schema::hasColumn('blogs', 'author_image')) {
                $table->longText('author_image')->nullable()->after('author_name');
            }
            if (!Schema::hasColumn('blogs', 'author_role')) {
                $table->string('author_role')->nullable()->after('author_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (Schema::hasColumn('blogs', 'author_role')) {
                $table->dropColumn('author_role');
            }
            if (Schema::hasColumn('blogs', 'author_image')) {
                $table->dropColumn('author_image');
            }
            if (Schema::hasColumn('blogs', 'author_name')) {
                $table->dropColumn('author_name');
            }
            if (Schema::hasColumn('blogs', 'read_time')) {
                $table->dropColumn('read_time');
            }
        });
    }
};

