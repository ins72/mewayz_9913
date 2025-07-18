<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('max_score', 8, 2)->default(100);
            $table->boolean('passed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent')->default(0); // in seconds
            $table->json('answers')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
            $table->foreign('enrollment_id')->references('id')->on('course_enrollments')->onDelete('cascade');
            $table->index(['user_id', 'quiz_id', 'attempt_number']);
            $table->index(['quiz_id', 'passed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_completions');
    }
};