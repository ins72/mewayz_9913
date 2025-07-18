<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('questions');
            $table->decimal('max_score', 8, 2)->default(100);
            $table->decimal('passing_score', 8, 2)->default(70);
            $table->integer('time_limit')->nullable(); // in minutes
            $table->integer('attempts_allowed')->default(3);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('courses_lessons')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index(['lesson_id', 'is_active']);
            $table->index(['course_id', 'is_required']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};