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
        Schema::create('workspace_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('workspace_id');
            $table->bigInteger('subscription_plan_id')->unsigned();
            $table->enum('status', ['active', 'inactive', 'suspended', 'cancelled', 'past_due'])->default('active');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('base_price', 10, 2)->default(0.00);
            $table->decimal('feature_price', 10, 2)->default(0.00);
            $table->decimal('total_price', 10, 2)->default(0.00);
            $table->integer('feature_count')->default(0);
            $table->json('enabled_features')->nullable();
            $table->json('transaction_fees')->nullable(); // Different fees per plan
            $table->json('limits')->nullable(); // Usage limits per plan
            $table->json('metadata')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('trial_start')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->timestamps();
            
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('restrict');
            
            $table->index(['workspace_id', 'status']);
            $table->index(['subscription_plan_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_subscriptions');
    }
};