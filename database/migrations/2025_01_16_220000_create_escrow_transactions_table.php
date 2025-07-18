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
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('item_type', ['website', 'digital_asset', 'service', 'physical_good', 'business']);
            $table->string('item_title');
            $table->text('item_description');
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('escrow_fee', 15, 2);
            $table->decimal('escrow_fee_percentage', 5, 2);
            $table->enum('status', [
                'pending_funding',
                'funded',
                'delivered', 
                'completed',
                'disputed',
                'cancelled',
                'expired'
            ])->default('pending_funding');
            $table->string('payment_method')->nullable();
            $table->string('payment_id')->nullable();
            $table->integer('inspection_period_hours')->default(72);
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('inspection_deadline')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->json('delivery_proof')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->boolean('insurance_required')->default(false);
            $table->decimal('insurance_amount', 15, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['status', 'created_at']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrow_transactions');

};