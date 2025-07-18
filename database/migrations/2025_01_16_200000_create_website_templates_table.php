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
        Schema::create('website_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('preview_image')->nullable();
            $table->string('demo_url')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable();
            $table->json('template_data')->nullable();
            $table->json('styles')->nullable();
            $table->json('scripts')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['category', 'is_active']);
            $table->index(['is_free', 'is_active']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_templates');

};