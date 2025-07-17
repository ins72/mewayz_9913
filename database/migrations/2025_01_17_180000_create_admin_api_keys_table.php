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
        Schema::create('admin_api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_name', 100)->index();
            $table->string('api_key_name', 255);
            $table->text('api_key_value');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_name', 'api_key_name']);
            $table->index(['service_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_api_keys');
    }
};