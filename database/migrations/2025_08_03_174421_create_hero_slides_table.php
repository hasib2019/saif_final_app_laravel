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
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Multilingual title
            $table->json('subtitle')->nullable(); // Multilingual subtitle
            $table->json('description')->nullable(); // Multilingual description
            $table->string('image')->nullable(); // Background image
            $table->json('button_text')->nullable(); // Multilingual button text
            $table->string('button_link')->nullable(); // Button link
            $table->integer('sort_order')->default(0); // Order of slides
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
