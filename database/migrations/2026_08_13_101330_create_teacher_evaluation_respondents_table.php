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
        Schema::create('teacher_evaluation_respondents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')
                  ->constrained('teacher_evaluations')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('teacher_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->foreignId('course_id')
                  ->constrained('courses')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_evaluation_respondents');
    }
};
