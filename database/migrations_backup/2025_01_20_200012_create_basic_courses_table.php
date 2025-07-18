<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('status')->default(1);
                $table->decimal('price', 10, 2)->default(0);
                $table->string('category')->nullable();
                $table->string('banner')->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('courses_lessons')) {
            Schema::create('courses_lessons', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->longText('content')->nullable();
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->index(['course_id', 'order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courses_lessons');
        Schema::dropIfExists('courses');
    }
};