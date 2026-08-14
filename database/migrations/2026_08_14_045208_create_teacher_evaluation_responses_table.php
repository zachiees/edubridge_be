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
        Schema::create('teacher_evaluation_responses', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('evaluation_id')
                  ->constrained('teacher_evaluations')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('respondent_id')
                  ->constrained('teacher_evaluation_respondents')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('question_id')
                  ->constrained('teacher_evaluation_questions')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->tinyInteger('rating')->default(0);
            $table->string('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_evaluation_responses');
    }
};
