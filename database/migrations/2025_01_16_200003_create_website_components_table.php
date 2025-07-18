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
        Schema::create('website_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('website_id');
            $table->uuid('page_id');
            $table->string('type');
            $table->json('content')->nullable();
            $table->json('settings')->nullable();
            $table->string('position')->default('main');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->json('responsive_settings')->nullable();
            $table->timestamps();

            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->foreign('page_id')->references('id')->on('website_pages')->onDelete('cascade');
            $table->index(['page_id', 'position', 'order']);
            $table->index(['website_id', 'type']);
            $table->index(['page_id', 'is_active']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_components');

};