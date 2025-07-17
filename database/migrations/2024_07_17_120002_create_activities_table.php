<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('contact_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('deal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // call, email, meeting, note, task
            $table->string('subject');
            $table->text('description')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->json('metadata')->nullable(); // Additional data like call duration, email open rate, etc.
            $table->timestamps();
            
            $table->index(['workspace_id', 'type']);
            $table->index(['workspace_id', 'status']);
            $table->index(['contact_id', 'type']);
            $table->index('due_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
};