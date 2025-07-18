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
        if (!Schema::hasTable('email_campaigns')) {
            Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('workspace_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('subject');
            $table->longText('content');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->json('recipient_lists'); // Array of email list IDs
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->json('settings')->nullable(); // Campaign settings (tracking, etc.)
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['workspace_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });


    /**
     * Reverse the migrations.
     */



    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');

};