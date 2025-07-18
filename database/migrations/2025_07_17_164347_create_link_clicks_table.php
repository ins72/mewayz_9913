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
        if (!Schema::hasTable('link_clicks')) {
            Schema::create('link_clicks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('shortened_link_id');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->enum('device_type', ['Desktop', 'Mobile', 'Tablet'])->default('Desktop');
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->timestamp('clicked_at');
            $table->string('session_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('shortened_link_id')->references('id')->on('shortened_links')->onDelete('cascade');
            
            $table->index(['shortened_link_id', 'clicked_at']);
            $table->index(['ip_address']);
            $table->index(['country']);
            $table->index(['device_type']);
            $table->index(['clicked_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_clicks');
    }
};
