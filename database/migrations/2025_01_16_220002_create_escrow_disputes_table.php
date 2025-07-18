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
        Schema::create('escrow_disputes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('escrow_transaction_id');
            $table->foreignId('initiated_by')->constrained('users');
            $table->enum('reason', ['not_delivered', 'not_as_described', 'damaged', 'unauthorized_charges', 'other']);
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->enum('requested_resolution', ['full_refund', 'partial_refund', 'replacement', 'completion']);
            $table->enum('status', ['open', 'in_mediation', 'resolved', 'escalated'])->default('open');
            $table->foreignId('mediator_id')->nullable()->constrained('users');
            $table->string('resolution')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->decimal('refund_percentage', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('escrow_transaction_id')->references('id')->on('escrow_transactions')->onDelete('cascade');
            $table->index(['escrow_transaction_id', 'status']);
            $table->index(['initiated_by', 'status']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrow_disputes');

}
};