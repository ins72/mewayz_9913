<?php
/**
 * Script to fix migration files to use UUID for workspace_id instead of unsignedBigInteger
 */

$files = [
    'database/migrations/2025_07_15_154122_create_instagram_accounts_table.php',
    'database/migrations/2025_07_15_154139_create_instagram_hashtags_table.php',
    'database/migrations/2025_07_16_131256_create_email_campaigns_table.php',
    'database/migrations/2025_07_16_131305_create_email_subscribers_table.php',
    'database/migrations/2025_07_16_131310_create_email_templates_table.php',
    'database/migrations/2025_07_16_131315_create_email_lists_table.php',
    'database/migrations/2025_01_18_000002_create_unified_analytics_events_table.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Replace unsignedBigInteger('workspace_id') with uuid('workspace_id')
        $content = str_replace(
            '$table->unsignedBigInteger(\'workspace_id\')',
            '$table->uuid(\'workspace_id\')',
            $content
        );
        
        // Also handle nullable version
        $content = str_replace(
            '$table->unsignedBigInteger(\'workspace_id\')->nullable()',
            '$table->uuid(\'workspace_id\')->nullable()',
            $content
        );
        
        file_put_contents($file, $content);
        echo "Fixed: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

echo "All migrations fixed!\n";