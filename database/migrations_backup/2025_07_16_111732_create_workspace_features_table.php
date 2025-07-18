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
        if (!Schema::hasTable('workspace_features')) {
            Schema::create('workspace_features', function (Blueprint $table) {
            $table->id();
            $table->char('workspace_id', 36);
            $table->foreignId('feature_id')->constrained()->onDelete('cascade');
            $table->boolean('is_enabled')->default(true);
            $table->json('configuration')->nullable(); // Feature-specific configuration
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->unique(['workspace_id', 'feature_id']);
        });


    /**
     * Reverse the migrations.
     */



}

public function down(): void
    {
        Schema::dropIfExists('workspace_features');

};