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
        if (!Schema::hasTable('email_lists')) {
            Schema::create('email_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('subscriber_count')->default(0);
            $table->json('tags')->nullable(); // List tags for organization
            $table->json('segmentation_rules')->nullable(); // Auto-segmentation rules
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['workspace_id', 'is_active']);
            $table->index(['workspace_id', 'created_at']);
        });
        
        // Pivot table for subscriber-list relationships
        if (!Schema::hasTable('email_list_subscribers')) {
            Schema::create('email_list_subscribers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->timestamp('subscribed_at');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('list_id')->references('id')->on('email_lists')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('email_subscribers')->onDelete('cascade');
            
            $table->unique(['list_id', 'subscriber_id']);
            $table->index(['list_id', 'subscribed_at']);
        });


    /**
     * Reverse the migrations.
     */



    public function down(): void
    {
        Schema::dropIfExists('email_list_subscribers');
        Schema::dropIfExists('email_lists');

}
};