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
        // Admin Users Table
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('admin'); // admin, super_admin, manager
            $table->json('permissions')->nullable();
            $table->json('restrictions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        // Admin Activity Log
        Schema::create('admin_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
            $table->index(['admin_user_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        // Subscription Plans Table (Enhanced)
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, quarterly
            $table->integer('trial_days')->default(0);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('active'); // active, inactive, deprecated
            $table->json('features')->nullable();
            $table->json('limits')->nullable();
            $table->json('restrictions')->nullable();
            $table->json('pricing_tiers')->nullable(); // For usage-based pricing
            $table->json('geographic_pricing')->nullable();
            $table->datetime('deprecated_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['status', 'is_featured']);
        });

        // Plan Features Table
        Schema::create('plan_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('general');
            $table->string('type')->default('boolean'); // boolean, numeric, text
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Plan Feature Assignments
        Schema::create('plan_feature_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('feature_id');
            $table->boolean('is_enabled')->default(true);
            $table->json('limits')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
            
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('feature_id')->references('id')->on('plan_features')->onDelete('cascade');
            $table->unique(['plan_id', 'feature_id']);
        });

        // System Settings Table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, text
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_public')->default(false);
            $table->timestamps();
            
            $table->index(['group', 'key']);
        });

        // Environment Variables Table
        Schema::create('environment_variables', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('requires_restart')->default(false);
            $table->timestamps();
        });

        // User Segments Table
        Schema::create('user_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('conditions')->nullable();
            $table->boolean('is_dynamic')->default(true);
            $table->integer('user_count')->default(0);
            $table->datetime('last_calculated')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User Segment Memberships
        Schema::create('user_segment_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('segment_id');
            $table->datetime('joined_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('segment_id')->references('id')->on('user_segments')->onDelete('cascade');
            $table->unique(['user_id', 'segment_id']);
        });

        // Bulk Operations Table
        Schema::create('bulk_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_user_id');
            $table->string('operation_type');
            $table->string('entity_type');
            $table->json('parameters')->nullable();
            $table->json('filters')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('success_records')->default(0);
            $table->integer('failed_records')->default(0);
            $table->json('results')->nullable();
            $table->json('errors')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('admin_user_id')->references('id')->on('admin_users')->onDelete('cascade');
            $table->index(['admin_user_id', 'status']);
        });

        // Feature Flags Table
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('conditions')->nullable();
            $table->json('user_segments')->nullable();
            $table->integer('rollout_percentage')->default(0);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->timestamps();
        });

        // API Keys Table
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('secret')->nullable();
            $table->string('type')->default('internal'); // internal, external, webhook
            $table->json('permissions')->nullable();
            $table->json('restrictions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('expires_at')->nullable();
            $table->datetime('last_used')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('bulk_operations');
        Schema::dropIfExists('user_segment_memberships');
        Schema::dropIfExists('user_segments');
        Schema::dropIfExists('environment_variables');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('plan_feature_assignments');
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('subscription_plans');
        Schema::dropIfExists('admin_activity_logs');
        Schema::dropIfExists('admin_users');
    }
};