<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workspace_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('contact_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('stage', [
                'lead', 'qualified', 'proposal', 'negotiation', 
                'closed_won', 'closed_lost'
            ])->default('lead');
            $table->integer('probability')->default(10); // percentage
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();
            $table->string('close_reason')->nullable();
            $table->json('products')->nullable(); // Associated products/services
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            
            $table->index(['workspace_id', 'stage']);
            $table->index(['workspace_id', 'assigned_to']);
            $table->index('expected_close_date');
        });


    public function down()
    {
        Schema::dropIfExists('deals');

};