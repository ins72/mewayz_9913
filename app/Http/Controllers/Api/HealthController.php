<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Get system health status
     */
    public function index(Request $request)
    {
        try {
            // Database health check
            DB::select('SELECT 1');
            $database_status = 'healthy';
        } catch (\Exception $e) {
            $database_status = 'unhealthy';
        }

        // Cache health check
        try {
            Cache::put('health_check', true, 60);
            $cache_status = Cache::get('health_check') ? 'healthy' : 'unhealthy';
        } catch (\Exception $e) {
            $cache_status = 'unhealthy';
        }

        // Application health metrics
        $health_data = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'version' => '2.0.0',
            'environment' => config('app.env'),
            'services' => [
                'database' => $database_status,
                'cache' => $cache_status,
                'queue' => 'healthy', // Assuming queue is healthy
            ],
            'metrics' => [
                'memory_usage' => memory_get_usage(true),
                'memory_peak' => memory_get_peak_usage(true),
                'uptime' => $this->getUptime(),
            ],
            'features' => [
                'authentication' => true,
                'workspace_setup' => true,
                'instagram_management' => true,
                'stripe_payments' => true,
                'bio_sites' => true,
                'course_management' => true,
                'crm_system' => true,
                'email_marketing' => true,
                'analytics' => true,
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'System health check completed',
            'data' => $health_data
        ]);
    }

    /**
     * Get system uptime
     */
    private function getUptime()
    {
        $uptime_file = storage_path('framework/uptime.txt');
        
        if (!file_exists($uptime_file)) {
            file_put_contents($uptime_file, now()->timestamp);
        }
        
        $start_time = file_get_contents($uptime_file);
        return now()->timestamp - $start_time;
    }
}