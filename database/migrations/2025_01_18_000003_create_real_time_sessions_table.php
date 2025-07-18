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
        Schema::create('real_time_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->string('session_id');
            $table->string('channel_name');
            $table->enum('session_type', ['collaboration', 'live_editing', 'chat', 'presentation', 'screen_share'])->default('collaboration');
            $table->enum('status', ['active', 'inactive', 'ended'])->default('active');
            $table->json('participants')->nullable();
            $table->json('permissions')->nullable();
            $table->json('session_data')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['workspace_id', 'status']);
            $table->index(['session_id']);
            $table->index(['channel_name']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_time_sessions');
    }
};