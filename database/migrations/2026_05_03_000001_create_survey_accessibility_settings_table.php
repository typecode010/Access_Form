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
        if (Schema::hasTable('survey_accessibility_settings')) {
            return;
        }

        Schema::create('survey_accessibility_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete()->unique();
            $table->boolean('high_contrast_enabled')->default(false);
            $table->boolean('dyslexia_friendly_enabled')->default(false);
            $table->boolean('keyboard_nav_enforced')->default(true);
            $table->string('text_size')->nullable();
            $table->boolean('reduced_motion')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_accessibility_settings');
    }
};
