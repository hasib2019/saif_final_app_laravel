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
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('subtitle')->nullable();
            $table->json('content')->nullable();
            $table->json('mission')->nullable();
            $table->json('vision')->nullable();
            $table->json('values')->nullable();
            $table->json('history')->nullable();
            $table->json('team_description')->nullable();
            $table->string('about_image')->nullable();
            $table->string('team_image')->nullable();
            $table->json('office_images')->nullable();
            $table->integer('founded_year')->nullable();
            $table->integer('employees_count')->nullable();
            $table->integer('countries_served')->nullable();
            $table->integer('projects_completed')->nullable();
            $table->json('achievements')->nullable();
            $table->json('timeline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};
