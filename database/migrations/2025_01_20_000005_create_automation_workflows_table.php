<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('workspace_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type'); // event, schedule, webhook
            $table->json('trigger_config');
            $table->json('actions');
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('execution_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();
            $table->timestamp('next_execution_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            
            $table->index(['user_id', 'is_active']);
            $table->index(['workspace_id', 'is_active']);
            $table->index(['trigger_type']);
            $table->index(['next_execution_at']);
        });


    public function down()
    {
        Schema::dropIfExists('automation_workflows');

};