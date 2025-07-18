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
        Schema::create('transaction_fees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->uuid('transaction_id');
            $table->string('transaction_type'); // escrow, ecommerce, booking, course, etc.
            $table->string('fee_type'); // platform_fee, payment_processing, etc.
            $table->decimal('transaction_amount', 15, 2);
            $table->decimal('fee_percentage', 5, 2);
            $table->decimal('fee_amount', 15, 2);
            $table->decimal('net_amount', 15, 2);
            $table->string('subscription_plan');
            $table->json('fee_breakdown')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            
            $table->index(['workspace_id', 'transaction_type']);
            $table->index(['transaction_type', 'subscription_plan']);
            $table->index(['created_at']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_fees');

};