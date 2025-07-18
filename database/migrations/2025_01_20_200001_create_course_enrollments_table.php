<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('enrollment_date')->nullable();
            $table->timestamp('completion_date')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->enum('completion_status', ['enrolled', 'in_progress', 'completed', 'dropped'])->default('enrolled');
            $table->timestamp('last_activity_at')->nullable();
            $table->integer('total_time_spent')->default(0);
            $table->integer('lessons_completed')->default(0);
            $table->json('quiz_scores')->nullable();
            $table->integer('certificates_earned')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'completion_status']);
            $table->index(['course_id', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};