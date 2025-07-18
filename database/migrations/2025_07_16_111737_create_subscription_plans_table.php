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
        if (!Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('type', ['free', 'professional', 'enterprise']);
            $table->decimal('base_price', 8, 2)->default(0.00);
            $table->decimal('feature_price_monthly', 8, 2)->default(1.00);
            $table->decimal('feature_price_yearly', 8, 2)->default(10.00);
            $table->integer('max_features')->nullable(); // null = unlimited
            $table->boolean('has_branding')->default(true); // false = white-label
            $table->boolean('has_priority_support')->default(false);
            $table->boolean('has_custom_domain')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->json('included_features')->nullable(); // Array of feature IDs included for free
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};