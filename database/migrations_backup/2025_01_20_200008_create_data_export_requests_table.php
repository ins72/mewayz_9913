<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_export_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('email');
            $table->json('data_types');
            $table->enum('format', ['json', 'csv', 'pdf']);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('file_path')->nullable();
            $table->string('download_url')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->ipAddress('ip_address');
            $table->text('processing_notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
            $table->index(['status', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_export_requests');
    }
};