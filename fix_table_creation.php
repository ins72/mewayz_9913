<?php
/**
 * Script to fix all remaining migrations to handle existing tables properly
 */

$migrations = [
    'database/migrations/2025_07_15_154122_create_instagram_accounts_table.php',
    'database/migrations/2025_07_15_154139_create_instagram_hashtags_table.php',
    'database/migrations/2025_07_16_111722_create_features_table.php',
    'database/migrations/2025_07_16_111727_create_workspace_goals_table.php',
    'database/migrations/2025_07_16_111732_create_workspace_features_table.php',
    'database/migrations/2025_07_16_111737_create_subscription_plans_table.php',
    'database/migrations/2025_07_16_111742_create_team_invitations_table.php',
    'database/migrations/2025_07_16_131256_create_email_campaigns_table.php',
    'database/migrations/2025_07_16_131305_create_email_subscribers_table.php',
    'database/migrations/2025_07_16_131310_create_email_templates_table.php',
    'database/migrations/2025_07_16_131315_create_email_lists_table.php',
    'database/migrations/2025_07_16_131321_create_email_campaign_analytics_table.php',
    'database/migrations/2025_07_16_161451_create_audiences_table.php',
    'database/migrations/2025_07_17_152322_create_escrow_documents_table.php',
    'database/migrations/2025_07_17_153426_create_booking_calendars_table.php',
    'database/migrations/2025_07_17_153444_create_booking_services_table.php',
    'database/migrations/2025_07_17_164300_create_shortened_links_table.php',
    'database/migrations/2025_07_17_164347_create_link_clicks_table.php',
    'database/migrations/2025_07_17_164629_create_referrals_table.php',
    'database/migrations/2025_07_17_164717_create_referral_rewards_table.php',
    'database/migrations/2025_07_17_165208_create_template_categories_table.php',
    'database/migrations/2025_07_17_165252_create_templates_table.php',
    'database/migrations/2025_07_17_165255_create_template_purchases_table.php',
    'database/migrations/2025_07_17_165257_create_template_reviews_table.php'
];

foreach ($migrations as $migration) {
    if (file_exists($migration)) {
        $content = file_get_contents($migration);
        
        // Extract table name from filename
        preg_match('/create_(.+)_table\.php$/', $migration, $matches);
        $tableName = $matches[1] ?? 'unknown';
        
        // Replace Schema::create with conditional creation
        $content = preg_replace(
            '/Schema::create\(\s*\'([^\']+)\'\s*,\s*function\s*\(\s*Blueprint\s*\$table\s*\)\s*\{/',
            'if (!Schema::hasTable(\'$1\')) {
            Schema::create(\'$1\', function (Blueprint $table) {',
            $content
        );
        
        // Add closing bracket for if statement
        $content = str_replace(
            'public function down(): void',
            '}
    }

    public function down(): void',
            $content
        );
        
        file_put_contents($migration, $content);
        echo "Fixed: $migration\n";
    } else {
        echo "File not found: $migration\n";
    }
}

echo "All table creation migrations fixed!\n";