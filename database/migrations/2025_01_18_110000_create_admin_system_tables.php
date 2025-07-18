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
        // Admin roles table
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions');
            $table->integer('hierarchy_level')->default(1);
            $table->boolean('is_system_role')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['hierarchy_level', 'is_active']);
        });

        // Admin users table (extends regular users with admin capabilities)
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained('admin_roles')->onDelete('cascade');
            $table->boolean('is_super_admin')->default(false);
            $table->timestamp('admin_since');
            $table->foreignId('granted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('additional_permissions')->nullable();
            $table->json('restrictions')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['role_id', 'is_active']);
            $table->index(['is_super_admin', 'is_active']);
        });

        // API keys management table
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key_id', 32)->unique();
            $table->string('key_secret', 64);
            $table->string('key_hash', 255); // Hashed version for security
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['personal', 'workspace', 'application', 'webhook']);
            $table->json('scopes'); // Permissions/endpoints this key can access
            $table->json('rate_limits')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['key_id', 'is_active']);
            $table->index(['expires_at', 'is_active']);
        });

        // Bulk operations tracking table
        Schema::create('bulk_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_id', 36)->unique();
            $table->enum('type', ['user_import', 'user_export', 'plan_update', 'email_campaign', 'data_migration', 'bulk_update']);
            $table->foreignId('initiated_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled']);
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('successful_records')->default(0);
            $table->integer('failed_records')->default(0);
            $table->text('description')->nullable();
            $table->json('parameters');
            $table->json('results')->nullable();
            $table->json('errors')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->text('current_step')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['initiated_by', 'status']);
            $table->index(['type', 'status']);
            $table->index(['created_at', 'status']);
        });

        // Dynamic pricing rules table
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['discount', 'markup', 'tiered', 'dynamic', 'promotional']);
            $table->json('conditions'); // Rules for when this pricing applies
            $table->json('adjustments'); // How prices should be modified
            $table->integer('priority')->default(0);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->json('applicable_plans')->nullable();
            $table->json('applicable_regions')->nullable();
            $table->json('applicable_user_segments')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->decimal('total_discount_amount', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
            $table->index(['priority', 'is_active']);
        });

        // System settings table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('key');
            $table->text('value');
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'json', 'array']);
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->string('validation_rules')->nullable();
            $table->json('options')->nullable(); // For dropdown/select settings
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['category', 'key']);
            $table->index(['category', 'is_public']);
        });

        // Environment variables management table
        Schema::create('environment_variables', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
            $table->text('description')->nullable();
            $table->enum('category', ['database', 'mail', 'payment', 'social', 'api', 'cache', 'queue', 'storage', 'security', 'custom']);
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_sensitive')->default(false);
            $table->boolean('requires_restart')->default(false);
            $table->timestamp('last_modified')->nullable();
            $table->foreignId('modified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('backup_history')->nullable();
            $table->timestamps();

            $table->unique('key');
            $table->index(['category', 'is_sensitive']);
        });

        // Database management table
        Schema::create('database_operations', function (Blueprint $table) {
            $table->id();
            $table->string('operation_id', 36)->unique();
            $table->enum('type', ['backup', 'restore', 'migrate', 'seed', 'optimize', 'repair', 'analyze']);
            $table->string('table_name')->nullable();
            $table->text('query')->nullable();
            $table->enum('status', ['pending', 'running', 'completed', 'failed']);
            $table->foreignId('initiated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('affected_rows')->nullable();
            $table->text('output')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['initiated_by', 'type']);
            $table->index(['status', 'created_at']);
        });

        // User segmentation table
        Schema::create('user_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('criteria'); // Conditions for segment membership
            $table->enum('type', ['static', 'dynamic', 'manual']);
            $table->integer('user_count')->default(0);
            $table->timestamp('last_updated')->nullable();
            $table->boolean('auto_update')->default(true);
            $table->json('tags')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index(['created_by', 'is_active']);
        });

        // User segment memberships pivot table
        Schema::create('user_segment_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('segment_id')->constrained('user_segments')->onDelete('cascade');
            $table->timestamp('added_at');
            $table->enum('added_method', ['automatic', 'manual', 'import']);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'segment_id']);
            $table->index(['segment_id', 'added_at']);
        });

        // Subscription plan analytics table
        Schema::create('plan_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->date('date');
            $table->integer('new_subscriptions')->default(0);
            $table->integer('cancelled_subscriptions')->default(0);
            $table->integer('upgraded_from')->default(0);
            $table->integer('downgraded_to')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('mrr', 12, 2)->default(0); // Monthly Recurring Revenue
            $table->decimal('churn_rate', 5, 2)->default(0);
            $table->decimal('retention_rate', 5, 2)->default(0);
            $table->integer('active_subscribers')->default(0);
            $table->json('feature_usage')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'date']);
            $table->index(['date', 'plan_id']);
        });

        // Feature flags table
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->enum('rollout_type', ['all', 'percentage', 'users', 'plans', 'segments']);
            $table->json('rollout_config')->nullable(); // Percentage, user IDs, plan IDs, etc.
            $table->json('conditions')->nullable(); // Additional conditions
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['is_enabled', 'rollout_type']);
            $table->index(['created_by', 'is_enabled']);
        });

        // Communication campaigns table
        Schema::create('communication_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['email', 'sms', 'push', 'in_app', 'webhook']);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed', 'cancelled']);
            $table->text('subject')->nullable();
            $table->text('content');
            $table->json('target_segments');
            $table->json('target_users')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('target_count')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->decimal('open_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['scheduled_at', 'status']);
        });

        // System health monitoring table
        Schema::create('system_health_metrics', function (Blueprint $table) {
            $table->id();
            $table->enum('metric_type', ['cpu', 'memory', 'disk', 'database', 'api', 'queue', 'cache', 'custom']);
            $table->string('metric_name');
            $table->decimal('value', 15, 4);
            $table->string('unit')->nullable();
            $table->enum('status', ['healthy', 'warning', 'critical']);
            $table->decimal('threshold_warning', 15, 4)->nullable();
            $table->decimal('threshold_critical', 15, 4)->nullable();
            $table->text('details')->nullable();
            $table->timestamp('recorded_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['metric_type', 'metric_name', 'recorded_at']);
            $table->index(['status', 'recorded_at']);
        });

        // Compliance and audit table
        Schema::create('compliance_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('resource_type')->nullable();
            $table->string('resource_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('admin_user_id')->nullable();
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('compliance_type', ['gdpr', 'ccpa', 'hipaa', 'sox', 'pci', 'iso27001', 'general']);
            $table->boolean('requires_attention')->default(false);
            $table->timestamp('occurred_at');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['user_id', 'occurred_at']);
            $table->index(['compliance_type', 'requires_attention']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_logs');
        Schema::dropIfExists('system_health_metrics');
        Schema::dropIfExists('communication_campaigns');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('plan_analytics');
        Schema::dropIfExists('user_segment_memberships');
        Schema::dropIfExists('user_segments');
        Schema::dropIfExists('database_operations');
        Schema::dropIfExists('environment_variables');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('bulk_operations');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('admin_users');
        Schema::dropIfExists('admin_roles');
    }
};