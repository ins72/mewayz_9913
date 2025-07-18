<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('referrals')) {
            Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('referrer_id');
            $table->unsignedBigInteger('referee_id')->nullable();
            $table->char('workspace_id', 36)->nullable();
            $table->string('referee_email');
            $table->string('referral_code', 50);
            $table->enum('status', ['sent', 'pending', 'completed', 'cancelled'])->default('sent');
            $table->enum('qualifying_action', ['subscription', 'purchase', 'workspace_creation'])->nullable();
            $table->decimal('action_value', 10, 2)->nullable();
            $table->decimal('reward_amount', 10, 2)->nullable();
            $table->text('custom_message')->nullable();
            $table->timestamp('invitation_sent_at')->nullable();
            $table->timestamp('signed_up_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('referrer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            
            $table->index(['referrer_id', 'status']);
            $table->index(['referee_id']);
            $table->index(['referral_code']);
            $table->index(['status']);
            $table->index(['completed_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referrals');
    }
};
