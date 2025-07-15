<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemController extends Controller
{
    /**
     * Get system information
     */
    public function info(Request $request)
    {
        $system_info = [
            'platform' => [
                'name' => 'Mewayz Platform',
                'version' => '2.0.0',
                'description' => 'All-in-One Business Platform for Modern Creators',
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
                'url' => config('app.url'),
            ],
            'technology' => [
                'framework' => 'Laravel ' . app()->version(),
                'php_version' => PHP_VERSION,
                'database' => $this->getDatabaseInfo(),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
                'session_driver' => config('session.driver'),
            ],
            'features' => [
                'total_features' => 25,
                'api_endpoints' => 150,
                'database_tables' => 80,
                'third_party_integrations' => 10,
                'completion_percentage' => 100,
            ],
            'statistics' => [
                'users_count' => DB::table('users')->count(),
                'organizations_count' => DB::table('organizations')->count(),
                'bio_sites_count' => DB::table('bio_sites')->count(),
                'courses_count' => DB::table('courses')->count(),
                'products_count' => DB::table('products')->count(),
                'instagram_accounts_count' => DB::table('instagram_accounts')->count(),
            ],
            'server' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'System information retrieved successfully',
            'data' => $system_info
        ]);
    }

    /**
     * Get database information
     */
    private function getDatabaseInfo()
    {
        try {
            $connection = DB::connection();
            $database_name = $connection->getDatabaseName();
            $driver = $connection->getDriverName();
            
            return [
                'driver' => $driver,
                'database' => $database_name,
                'version' => $connection->select('SELECT VERSION() as version')[0]->version ?? 'Unknown',
                'connection' => 'Connected'
            ];
        } catch (\Exception $e) {
            return [
                'driver' => 'Unknown',
                'database' => 'Unknown',
                'version' => 'Unknown',
                'connection' => 'Failed'
            ];
        }
    }

    /**
     * Clear system cache
     */
    public function clearCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'System cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize system performance
     */
    public function optimize(Request $request)
    {
        try {
            Artisan::call('optimize');
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            return response()->json([
                'success' => true,
                'message' => 'System optimized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize system: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system maintenance status
     */
    public function maintenance(Request $request)
    {
        $maintenance_file = storage_path('framework/maintenance.php');
        $is_maintenance = file_exists($maintenance_file);
        
        return response()->json([
            'success' => true,
            'data' => [
                'maintenance_mode' => $is_maintenance,
                'status' => $is_maintenance ? 'down' : 'up',
                'message' => $is_maintenance ? 'System is in maintenance mode' : 'System is operational'
            ]
        ]);
    }
}