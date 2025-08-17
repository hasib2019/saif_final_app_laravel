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
        Schema::create('technology_features', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Multilingual title
            $table->json('description'); // Multilingual description
            $table->string('icon')->nullable(); // Icon class or image
            $table->string('category'); // e.g., 'iot', 'ai', 'industry40', 'diagnostics'
            $table->json('benefits')->nullable(); // Multilingual benefits array
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technology_features');
    }
};
