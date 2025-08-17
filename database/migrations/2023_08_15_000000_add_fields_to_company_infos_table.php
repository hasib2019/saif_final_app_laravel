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
        Schema::table('company_infos', function (Blueprint $table) {
            // Add missing fields
            $table->json('vision')->nullable();
            $table->json('about_us')->nullable();
            $table->json('team_description')->nullable();
            $table->json('company_overview')->nullable();
            $table->string('about_image')->nullable();
            $table->string('team_image')->nullable();
            $table->json('office_images')->nullable();
            $table->integer('founded_year')->nullable();
            $table->integer('employees_count')->nullable();
            $table->integer('countries_served')->nullable();
            $table->integer('projects_completed')->nullable();
            $table->json('achievements')->nullable();
            $table->json('certifications')->nullable();
            $table->json('awards')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_infos', function (Blueprint $table) {
            // Drop added columns
            $table->dropColumn([
                'vision',
                'about_us',
                'team_description',
                'company_overview',
                'about_image',
                'team_image',
                'office_images',
                'founded_year',
                'employees_count',
                'countries_served',
                'projects_completed',
                'achievements',
                'certifications',
                'awards'
            ]);
        });
    }
};