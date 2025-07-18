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
        if (!Schema::hasTable('referral_rewards')) {
            Schema::create('referral_rewards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('referral_id');
            $table->enum('reward_type', ['commission', 'bonus', 'credit'])->default('commission');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referral_id')->references('id')->on('referrals')->onDelete('cascade');
            
            $table->index(['user_id', 'status']);
            $table->index(['referral_id']);
            $table->index(['status']);
            $table->index(['paid_at']);
        });


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_rewards');

};
