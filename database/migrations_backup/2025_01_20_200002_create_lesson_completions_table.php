<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->integer('time_spent')->default(0);
            $table->integer('attempts')->default(1);
            $table->boolean('is_completed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('courses_lessons')->onDelete('cascade');
            $table->foreign('enrollment_id')->references('id')->on('course_enrollments')->onDelete('cascade');
            $table->unique(['user_id', 'lesson_id']);
            $table->index(['user_id', 'is_completed']);
            $table->index(['lesson_id', 'completion_percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
    }
};