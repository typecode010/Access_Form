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
        if (Schema::hasTable('accessibility_issues')) {
            return;
        }

        Schema::create('accessibility_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Survey::class)->constrained()->cascadeOnDelete();
            $table->foreignId('survey_question_id')->nullable()->constrained('survey_questions')->nullOnDelete();
            $table->string('issue_type');
            $table->string('severity');
            $table->string('status')->default('open');
            $table->text('message');
            $table->timestamp('detected_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['survey_id', 'status']);
            $table->index('issue_type');
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessibility_issues');
    }
};
