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
        if (Schema::hasTable('survey_questions') && ! Schema::hasColumn('survey_questions', 'settings_json')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->json('settings_json')->nullable()->after('position');
            });
        }

        if (Schema::hasTable('question_options')
            && Schema::hasColumn('question_options', 'question_id')
            && ! Schema::hasColumn('question_options', 'survey_question_id')) {
            Schema::table('question_options', function (Blueprint $table) {
                $table->renameColumn('question_id', 'survey_question_id');
            });
        }

        if (Schema::hasTable('survey_questions')) {
            $this->addSurveyQuestionIndexes();
        }

        if (Schema::hasTable('question_options') && Schema::hasColumn('question_options', 'survey_question_id')) {
            $this->addQuestionOptionIndexesAndForeignKey();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('question_options')
            && Schema::hasColumn('question_options', 'survey_question_id')
            && ! Schema::hasColumn('question_options', 'question_id')) {
            Schema::table('question_options', function (Blueprint $table) {
                $table->renameColumn('survey_question_id', 'question_id');
            });
        }

        if (Schema::hasTable('survey_questions') && Schema::hasColumn('survey_questions', 'settings_json')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->dropColumn('settings_json');
            });
        }
    }

    private function addSurveyQuestionIndexes(): void
    {
        try {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->index(['survey_id', 'position']);
            });
        } catch (\Throwable) {
            // Ignore duplicate index errors across environments.
        }

        try {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->index('type');
            });
        } catch (\Throwable) {
            // Ignore duplicate index errors across environments.
        }
    }

    private function addQuestionOptionIndexesAndForeignKey(): void
    {
        try {
            Schema::table('question_options', function (Blueprint $table) {
                $table->index(['survey_question_id', 'position']);
            });
        } catch (\Throwable) {
            // Ignore duplicate index errors across environments.
        }

        try {
            Schema::table('question_options', function (Blueprint $table) {
                $table->foreign('survey_question_id')->references('id')->on('survey_questions')->cascadeOnDelete();
            });
        } catch (\Throwable) {
            // Ignore duplicate or existing foreign key errors across environments.
        }
    }
};
