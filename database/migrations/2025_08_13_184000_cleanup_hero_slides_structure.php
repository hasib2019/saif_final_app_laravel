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
            // Drop individual multilingual columns if they exist
            $columnsToDrop = [
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
            ];

            // Check and drop columns only if they exist
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('hero_slides', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Ensure JSON columns exist and are properly configured
            if (!Schema::hasColumn('hero_slides', 'button_link')) {
                $table->string('button_link')->nullable()->after('button_text');
            }
            if (!Schema::hasColumn('hero_slides', 'background_image')) {
                $table->string('background_image')->nullable()->after('image');
            }
            if (!Schema::hasColumn('hero_slides', 'video_url')) {
                $table->string('video_url')->nullable()->after('background_image');
            }
            if (!Schema::hasColumn('hero_slides', 'show_overlay')) {
                $table->boolean('show_overlay')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('hero_slides', 'overlay_opacity')) {
                $table->float('overlay_opacity', 8, 2)->default(0.5)->after('show_overlay');
            }
            if (!Schema::hasColumn('hero_slides', 'text_position')) {
                $table->string('text_position')->default('center')->after('overlay_opacity');
            }
            if (!Schema::hasColumn('hero_slides', 'animation_type')) {
                $table->string('animation_type')->nullable()->after('text_position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is primarily for cleanup, so reverse would be complex
        // In practice, you might want to restore the original structure
    }
};