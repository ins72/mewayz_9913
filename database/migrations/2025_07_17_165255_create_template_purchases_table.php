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
        if (!Schema::hasTable('template_purchases')) {
            Schema::create('template_purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->uuid('template_id');
            $table->unsignedBigInteger('creator_id');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_method', ['stripe', 'paypal', 'wallet']);
            $table->string('payment_token')->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['user_id', 'status']);
            $table->index(['template_id', 'status']);
            $table->index(['creator_id', 'status']);
            $table->index(['status']);
            $table->index(['completed_at']);
        });


    /**
     * Reverse the migrations.
     */



    public function down(): void
    {
        Schema::dropIfExists('template_purchases');

}
};