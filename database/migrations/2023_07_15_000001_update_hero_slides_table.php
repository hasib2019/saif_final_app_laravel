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
        Schema::table('hero_slides', function (Blueprint $table) {
            // Add new fields
            $table->string('title_en')->nullable()->after('title');
            $table->string('title_ar')->nullable()->after('title_en');
            $table->string('title_bn')->nullable()->after('title_ar');
            $table->string('subtitle_en')->nullable()->after('subtitle');
            $table->string('subtitle_ar')->nullable()->after('subtitle_en');
            $table->string('subtitle_bn')->nullable()->after('subtitle_ar');
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_ar')->nullable()->after('description_en');
            $table->text('description_bn')->nullable()->after('description_ar');
            $table->string('button_text_en')->nullable()->after('button_text');
            $table->string('button_text_ar')->nullable()->after('button_text_en');
            $table->string('button_text_bn')->nullable()->after('button_text_ar');
            $table->string('button_url')->nullable()->after('button_link');
            $table->string('background_image')->nullable()->after('image');
            $table->string('video_url')->nullable()->after('background_image');
            $table->integer('order')->default(0)->after('sort_order');
            $table->boolean('show_overlay')->default(false)->after('is_active');
            $table->float('overlay_opacity', 8, 2)->default(0.5)->after('show_overlay');
            $table->string('text_position')->default('center')->after('overlay_opacity');
            $table->string('animation_type')->nullable()->after('text_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn([
                'title_en',
                'title_ar',
                'title_bn',
                'subtitle_en',
                'subtitle_ar',
                'subtitle_bn',
                'description_en',
                'description_ar',
                'description_bn',
                'button_text_en',
                'button_text_ar',
                'button_text_bn',
                'button_url',
                'background_image',
                'video_url',
                'order',
                'show_overlay',
                'overlay_opacity',
                'text_position',
                'animation_type'
            ]);
        });
    }
};