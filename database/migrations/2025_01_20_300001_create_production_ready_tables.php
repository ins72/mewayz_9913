<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User Subscriptions Table
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id');
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('status', ['active', 'canceled', 'trial', 'expired', 'pending'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'yearly', 'lifetime']);
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->timestamp('canceled_at')->nullable();
            $table->unsignedBigInteger('default_payment_method_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->index(['user_id', 'status']);
        });

        // Payment Methods Table
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('stripe_payment_method_id');
            $table->string('type'); // card, bank_account, etc.
            $table->string('brand')->nullable();
            $table->string('last_four')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('billing_address');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_active']);
        });

        // Transactions Table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('type')->default('payment'); // payment, refund, etc.
            $table->string('description');
            $table->enum('status', ['pending', 'succeeded', 'failed', 'canceled']);
            $table->json('metadata')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->index(['user_id', 'status']);
        });

        // Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['draft', 'open', 'paid', 'void', 'uncollectible']);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('line_items');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('user_subscriptions')->onDelete('cascade');
            $table->index(['user_id', 'status']);
        });

        // Account Deletion Requests Table
        Schema::create('account_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('reason');
            $table->text('feedback')->nullable();
            $table->boolean('delete_immediately')->default(false);
            $table->timestamp('scheduled_for');
            $table->enum('status', ['pending', 'processing', 'completed', 'canceled']);
            $table->timestamp('canceled_at')->nullable();
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'status']);
        });

        // Affiliates Table
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('referral_code')->unique();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended']);
            $table->decimal('commission_rate', 5, 2)->default(30.00);
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->json('application_data')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('applied_at');
            $table->timestamp('approved_at')->nullable();
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('pending_balance', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'tier']);
        });

        // Affiliate Referrals Table
        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->unsignedBigInteger('user_id');
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->enum('status', ['pending', 'converted', 'rejected']);
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['affiliate_id', 'status']);
        });

        // Affiliate Links Table
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->string('name');
            $table->string('target_url');
            $table->string('url');
            $table->string('campaign')->nullable();
            $table->string('medium')->nullable();
            $table->string('source')->nullable();
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
        });

        // Affiliate Commissions Table
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->unsignedBigInteger('referral_id');
            $table->unsignedBigInteger('subscription_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected']);
            $table->timestamp('earned_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
            $table->foreign('referral_id')->references('id')->on('affiliate_referrals')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('user_subscriptions')->onDelete('cascade');
            $table->index(['affiliate_id', 'status']);
        });

        // Affiliate Payments Table
        Schema::create('affiliate_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method');
            $table->json('payment_details');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
            $table->index(['affiliate_id', 'status']);
        });

        // System Settings Table
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Feature Flags Table
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->json('conditions')->nullable();
            $table->integer('rollout_percentage')->default(0);
            $table->timestamp('enabled_at')->nullable();
            $table->timestamps();
        });

        // Support Tickets Table
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'pending', 'resolved', 'closed']);
            $table->string('category');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->integer('satisfaction_rating')->nullable();
            $table->text('satisfaction_feedback')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'priority']);
        });

        // Subscription Addons Table
        Schema::create('subscription_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->enum('billing_type', ['one_time', 'recurring']);
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // User Subscription Addons Table
        Schema::create('user_subscription_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('addon_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('user_subscriptions')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('subscription_addons')->onDelete('cascade');
        });

        // Integration Configurations Table
        Schema::create('integration_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->boolean('is_active')->default(false);
            $table->json('configuration');
            $table->json('webhook_endpoints')->nullable();
            $table->string('api_version')->nullable();
            $table->timestamps();
        });

        // User Integrations Table
        Schema::create('user_integrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('integration_id');
            $table->json('credentials');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->json('sync_errors')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('integration_id')->references('id')->on('integration_configurations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_integrations');
        Schema::dropIfExists('integration_configurations');
        Schema::dropIfExists('user_subscription_addons');
        Schema::dropIfExists('subscription_addons');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('affiliate_payments');
        Schema::dropIfExists('affiliate_commissions');
        Schema::dropIfExists('affiliate_links');
        Schema::dropIfExists('affiliate_referrals');
        Schema::dropIfExists('affiliates');
        Schema::dropIfExists('account_deletion_requests');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('user_subscriptions');
    }
};