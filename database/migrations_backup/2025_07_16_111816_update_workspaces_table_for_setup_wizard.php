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
        Schema::table('workspaces', function (Blueprint $table) {
            $table->json('selected_goals')->nullable(); // Array of goal IDs
            $table->json('selected_features')->nullable(); // Array of feature IDs
            $table->json('team_setup')->nullable(); // Team configuration
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans');
            $table->json('branding_config')->nullable(); // Logo, colors, etc.
            $table->enum('setup_step', ['goals', 'features', 'team', 'subscription', 'branding', 'complete'])->default('goals');
            $table->boolean('setup_completed')->default(false);
            $table->timestamp('setup_completed_at')->nullable();
        });


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'selected_goals',
                'selected_features',
                'team_setup',
                'subscription_plan_id',
                'branding_config',
                'setup_step',
                'setup_completed',
                'setup_completed_at'
            ]);
        });

};
