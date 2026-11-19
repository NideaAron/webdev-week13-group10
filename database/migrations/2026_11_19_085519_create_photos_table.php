<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('caption', 255);
            $table->string('path')->unique();
            $table->string('original_name', 255);
            $table->unsignedInteger('size');
            $table->timestamps();
        });

        // Rules the database enforces itself
        DB::statement('ALTER TABLE photos ADD CONSTRAINT photos_size_check CHECK (size > 0 AND size <= 2097152)');
        DB::statement("ALTER TABLE photos ADD CONSTRAINT photos_caption_check CHECK (length(trim(caption)) > 0)");
        DB::statement("ALTER TABLE photos ADD CONSTRAINT photos_path_check CHECK (length(trim(path)) > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};