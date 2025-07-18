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
        if (!Schema::hasTable('email_subscribers')) {
            Schema::create('email_subscribers', function (Blueprint $table) {
            $table->id();
            $table->uuid('workspace_id');
            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['subscribed', 'unsubscribed', 'bounced', 'complained'])->default('subscribed');
            $table->json('tags')->nullable(); // Custom tags for segmentation
            $table->json('custom_fields')->nullable(); // Additional custom data
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('source')->nullable(); // How they subscribed (form, import, etc.)
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            
            $table->unique(['workspace_id', 'email']);
            $table->index(['workspace_id', 'status']);
            $table->index(['email', 'status']);
            $table->index(['workspace_id', 'created_at']);
        });


    /**
     * Reverse the migrations.
     */



    public function down(): void
    {
        Schema::dropIfExists('email_subscribers');

}
};