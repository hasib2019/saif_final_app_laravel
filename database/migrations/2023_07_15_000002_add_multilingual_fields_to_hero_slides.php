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
        // First check if the hero_slides table exists
        if (!Schema::hasTable('hero_slides')) {
            return;
        }
        
        // Check if title_en column exists, if not, create it first
        Schema::table('hero_slides', function (Blueprint $table) {
            if (!Schema::hasColumn('hero_slides', 'title_en')) {
                $table->string('title_en')->nullable();
            }
            if (!Schema::hasColumn('hero_slides', 'subtitle_en')) {
                $table->string('subtitle_en')->nullable();
            }
            if (!Schema::hasColumn('hero_slides', 'description_en')) {
                $table->text('description_en')->nullable();
            }
            if (!Schema::hasColumn('hero_slides', 'button_text_en')) {
                $table->string('button_text_en')->nullable();
            }
        });
        
        Schema::table('hero_slides', function (Blueprint $table) {
            // Add multilingual fields
            if (!Schema::hasColumn('hero_slides', 'title_ar')) {
                $table->string('title_ar')->nullable()->after('title_en');
            }
            if (!Schema::hasColumn('hero_slides', 'title_bn')) {
                $table->string('title_bn')->nullable()->after('title_ar');
            }
            if (!Schema::hasColumn('hero_slides', 'subtitle_ar')) {
                $table->string('subtitle_ar')->nullable()->after('subtitle_en');
            }
            if (!Schema::hasColumn('hero_slides', 'subtitle_bn')) {
                $table->string('subtitle_bn')->nullable()->after('subtitle_ar');
            }
            if (!Schema::hasColumn('hero_slides', 'description_ar')) {
                $table->text('description_ar')->nullable()->after('description_en');
            }
            if (!Schema::hasColumn('hero_slides', 'description_bn')) {
                $table->text('description_bn')->nullable()->after('description_ar');
            }
            if (!Schema::hasColumn('hero_slides', 'button_text_ar')) {
                $table->string('button_text_ar')->nullable()->after('button_text_en');
            }
            if (!Schema::hasColumn('hero_slides', 'button_text_bn')) {
                $table->string('button_text_bn')->nullable()->after('button_text_ar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            // Remove multilingual fields
            $table->dropColumn([
                'title_ar',
                'title_bn',
                'subtitle_ar',
                'subtitle_bn',
                'description_ar',
                'description_bn',
                'button_text_ar',
                'button_text_bn'
            ]);
        });
    }
};