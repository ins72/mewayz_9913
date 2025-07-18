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
        Schema::create('escrow_milestones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('escrow_transaction_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->integer('order');
            $table->enum('status', ['pending', 'delivered', 'accepted', 'disputed'])->default('pending');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->json('delivery_proof')->nullable();
            $table->timestamps();

            $table->foreign('escrow_transaction_id')->references('id')->on('escrow_transactions')->onDelete('cascade');
            $table->index(['escrow_transaction_id', 'order']);
            $table->index(['escrow_transaction_id', 'status']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrow_milestones');

};