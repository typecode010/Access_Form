<?php

use App\Models\Survey;
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
        if (Schema::hasTable('survey_media')) {
            return;
        }

        Schema::create('survey_media', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Survey::class)->constrained()->cascadeOnDelete();
            $table->string('media_type');
            $table->string('file_path')->nullable();
            $table->text('alt_text')->nullable();
            $table->string('caption_path')->nullable();
            $table->longText('transcript_text')->nullable();
            $table->string('sign_language_video_url')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['survey_id', 'position']);
            $table->index('media_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_media');
    }
};
