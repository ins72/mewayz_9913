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
        Schema::create('websites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('domain')->unique();
            $table->uuid('template_id')->nullable();
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->longText('custom_css')->nullable();
            $table->longText('custom_js')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->json('meta_tags')->nullable();
            $table->text('analytics_code')->nullable();
            $table->json('backup_data')->nullable();
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('website_templates')->onDelete('set null');
            $table->index(['user_id', 'status']);
            $table->index(['status', 'published_at']);
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');

}
};