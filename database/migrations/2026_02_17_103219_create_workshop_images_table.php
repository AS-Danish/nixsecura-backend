<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workshop_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->timestamps();
        });

        // Migrate existing images
        $workshops = DB::table('workshops')->whereNotNull('image')->get();
        foreach ($workshops as $workshop) {
            if ($workshop->image) {
                DB::table('workshop_images')->insert([
                    'workshop_id' => $workshop->id,
                    'image_path' => $workshop->image,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        // Restore images (take the first one)
        $images = DB::table('workshop_images')->get();
        foreach ($images as $image) {
            DB::table('workshops')
                ->where('id', $image->workshop_id)
                ->update(['image' => $image->image_path]);
        }

        Schema::dropIfExists('workshop_images');
    }
};
