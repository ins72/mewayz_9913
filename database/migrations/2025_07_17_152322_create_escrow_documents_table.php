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
        if (!Schema::hasTable('escrow_documents')) {
            Schema::create('escrow_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('escrow_transaction_id')->constrained('escrow_transactions')->onDelete('cascade');
            $table->unsignedBigInteger('uploaded_by');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type');
            $table->enum('document_type', ['contract', 'invoice', 'receipt', 'evidence', 'delivery_proof', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index(['escrow_transaction_id', 'document_type']);
        });


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escrow_documents');

};
