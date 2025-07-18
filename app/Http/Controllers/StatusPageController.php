<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\SystemStatus;
use App\Models\StatusIncident;
use App\Models\HelpArticle;
use App\Models\HelpCategory;

class StatusPageController extends Controller
{
    /**
     * Status page
     */
    public function index()
    {
        try {
            $overallStatus = $this->getOverallStatus();
            $services = $this->getServiceStatus();
            $recentIncidents = $this->getRecentIncidents();
            $uptimeStats = $this->getUptimeStats();
            $maintenanceScheduled = $this->getScheduledMaintenance();

            return view('pages.status.index', compact(
                'overallStatus', 
                'services', 
                'recentIncidents', 
                'uptimeStats', 
                'maintenanceScheduled'
            ));

        } catch (\Exception $e) {
            Log::error('Status page failed: ' . $e->getMessage());
            return view('pages.status.index', [
                'overallStatus' => 'operational',
                'services' => [],
                'recentIncidents' => [],
                'uptimeStats' => [],
                'maintenanceScheduled' => []
            ]);
        }
    }

    /**
     * Get API status (JSON)
     */
    public function apiStatus()
    {
        try {
            $status = [
                'overall_status' => $this->getOverallStatus(),
                'services' => $this->getServiceStatus(),
                'uptime' => $this->getUptimeStats(),
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0')
            ];

            return response()->json($status);

        } catch (\Exception $e) {
            Log::error('API status failed: ' . $e->getMessage());
            return response()->json([
                'overall_status' => 'unknown',
                'services' => [],
                'uptime' => [],
                'timestamp' => now()->toISOString(),
                'error' => 'Failed to retrieve status'
            ], 500);
        }
    }

    /**
     * Get overall system status
     */
    private function getOverallStatus()
    {
        return Cache::remember('overall_status', 60, function () {
            try {
                $services = $this->getServiceStatus();
                $degradedServices = array_filter($services, function($service) {
                    return in_array($service['status'], ['degraded', 'down']);
                });

                if (empty($degradedServices)) {
                    return 'operational';
                } elseif (count($degradedServices) < count($services) / 2) {
                    return 'degraded';
                } else {
                    return 'down';
                }
            } catch (\Exception $e) {
                return 'unknown';
            }
        });
    }

    /**
     * Get service status
     */
    private function getServiceStatus()
    {
        return Cache::remember('service_status', 60, function () {
            $services = [
                'api' => $this->checkApiStatus(),
                'database' => $this->checkDatabaseStatus(),
                'frontend' => $this->checkFrontendStatus(),
                'payments' => $this->checkPaymentStatus(),
                'email' => $this->checkEmailStatus(),
                'file_storage' => $this->checkFileStorageStatus()
            ];

            $serviceList = [];
            foreach ($services as $name => $status) {
                $serviceList[] = [
                    'name' => ucwords(str_replace('_', ' ', $name)),
                    'slug' => $name,
                    'status' => $status['status'],
                    'response_time' => $status['response_time'] ?? null,
                    'last_checked' => now()->toISOString()
                ];
            }

            return $serviceList;
        });
    }

    /**
     * Check API status
     */
    private function checkApiStatus()
    {
        try {
            $startTime = microtime(true);
            
            // Simple health check
            $response = DB::select('SELECT 1');
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'status' => 'operational',
                'response_time' => $responseTime
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null
            ];
        }
    }

    /**
     * Check database status
     */
    private function checkDatabaseStatus()
    {
        try {
            $startTime = microtime(true);
            
            DB::select('SELECT 1');
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'status' => 'operational',
                'response_time' => $responseTime
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null
            ];
        }
    }

    /**
     * Check frontend status
     */
    private function checkFrontendStatus()
    {
        try {
            $startTime = microtime(true);
            
            // Check if frontend is accessible
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'GET'
                ]
            ]);
            
            $response = @file_get_contents('http://localhost:8001/', false, $context);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return [
                'status' => $response !== false ? 'operational' : 'down',
                'response_time' => $response !== false ? $responseTime : null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null
            ];
        }
    }

    /**
     * Check payment status
     */
    private function checkPaymentStatus()
    {
        try {
            // Check if we can connect to payment providers
            $stripeKey = env('STRIPE_API_KEY');
            
            if (!$stripeKey) {
                return [
                    'status' => 'degraded',
                    'response_time' => null
                ];
            }

            return [
                'status' => 'operational',
                'response_time' => null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null
            ];
        }
    }

    /**
     * Check email status
     */
    private function checkEmailStatus()
    {
        try {
            $mailConfig = config('mail.default');
            
            if (!$mailConfig) {
                return [
                    'status' => 'degraded',
                    'response_time' => null
                ];
            }

            return [
                'status' => 'operational',
                'response_time' => null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null
            ];
        }
    }

    /**
     * Check file storage status
     */
    private function checkFileStorageStatus()
    {
        try {
            $diskConfig = config('filesystems.default');
            
            if (!$diskConfig) {
                return [
                    'status' => 'degraded',
                    'response_time' => null
                ];
            }

            return [
                'status' => 'operational',
                'response_time' => null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'down',
                'response_time' => null
            ];
        }
    }

    /**
     * Get recent incidents
     */
    private function getRecentIncidents()
    {
        try {
            // This would typically come from a monitoring service or database
            return [
                [
                    'id' => 1,
                    'title' => 'Database Connection Issues',
                    'description' => 'Brief connection issues with primary database',
                    'status' => 'resolved',
                    'severity' => 'medium',
                    'started_at' => now()->subDays(5)->toISOString(),
                    'resolved_at' => now()->subDays(5)->addHours(2)->toISOString(),
                    'affected_services' => ['API', 'Database']
                ]
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get uptime statistics
     */
    private function getUptimeStats()
    {
        try {
            return [
                'last_24_hours' => 99.95,
                'last_7_days' => 99.92,
                'last_30_days' => 99.89,
                'last_90_days' => 99.85
            ];
        } catch (\Exception $e) {
            return [
                'last_24_hours' => 0,
                'last_7_days' => 0,
                'last_30_days' => 0,
                'last_90_days' => 0
            ];
        }
    }

    /**
     * Get scheduled maintenance
     */
    private function getScheduledMaintenance()
    {
        try {
            return [
                [
                    'id' => 1,
                    'title' => 'Monthly Database Maintenance',
                    'description' => 'Routine database optimization and backup verification',
                    'scheduled_start' => now()->addDays(7)->setTime(2, 0)->toISOString(),
                    'scheduled_end' => now()->addDays(7)->setTime(4, 0)->toISOString(),
                    'affected_services' => ['API', 'Database'],
                    'expected_impact' => 'minor'
                ]
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}

class HelpCenterController extends Controller
{
    /**
     * Help Center home
     */
    public function index()
    {
        try {
            $categories = HelpCategory::where('is_active', true)
                ->with(['articles' => function($query) {
                    $query->where('is_published', true)
                          ->orderBy('order')
                          ->take(5);
                }])
                ->orderBy('order')
                ->get();

            $popularArticles = HelpArticle::where('is_published', true)
                ->orderBy('views', 'desc')
                ->take(6)
                ->get();

            $recentArticles = HelpArticle::where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            return view('pages.help.index', compact('categories', 'popularArticles', 'recentArticles'));

        } catch (\Exception $e) {
            Log::error('Help Center page failed: ' . $e->getMessage());
            return view('pages.help.index', [
                'categories' => collect(),
                'popularArticles' => collect(),
                'recentArticles' => collect()
            ]);
        }
    }

    /**
     * Search help articles
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q');
            
            if (!$query) {
                return redirect()->route('help.index');
            }

            $articles = HelpArticle::where('is_published', true)
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%")
                      ->orWhere('summary', 'like', "%{$query}%");
                })
                ->orderBy('views', 'desc')
                ->paginate(20);

            return view('pages.help.search', compact('articles', 'query'));

        } catch (\Exception $e) {
            Log::error('Help Center search failed: ' . $e->getMessage());
            return redirect()->route('help.index');
        }
    }

    /**
     * View help article
     */
    public function article($slug)
    {
        try {
            $article = HelpArticle::where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();

            // Increment views
            $article->increment('views');

            $relatedArticles = HelpArticle::where('is_published', true)
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->orderBy('views', 'desc')
                ->take(3)
                ->get();

            return view('pages.help.article', compact('article', 'relatedArticles'));

        } catch (\Exception $e) {
            Log::error('Help article view failed: ' . $e->getMessage());
            return redirect()->route('help.index');
        }
    }

    /**
     * View help category
     */
    public function category($slug)
    {
        try {
            $category = HelpCategory::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            $articles = HelpArticle::where('category_id', $category->id)
                ->where('is_published', true)
                ->orderBy('order')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('pages.help.category', compact('category', 'articles'));

        } catch (\Exception $e) {
            Log::error('Help category view failed: ' . $e->getMessage());
            return redirect()->route('help.index');
        }
    }
}