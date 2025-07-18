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
        if (!Schema::hasTable('features')) {
            Schema::create('features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('category');
            $table->json('goals')->nullable(); // Array of goal IDs this feature supports
            $table->decimal('monthly_price', 8, 2)->default(1.00);
            $table->decimal('yearly_price', 8, 2)->default(10.00);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('dependencies')->nullable(); // Array of feature IDs this depends on
            $table->json('metadata')->nullable(); // Additional feature configuration
            $table->timestamps();
        });


    /**
     * Reverse the migrations.
     */



}

public function down(): void
    {
        Schema::dropIfExists('features');

};