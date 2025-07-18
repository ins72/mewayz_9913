<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Admin\UserSegment;
use App\Models\Admin\BulkOperation;
use App\Models\Admin\AdminUser;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'role' => 'nullable|string',
            'sort' => 'nullable|string|in:name,email,created_at,last_login',
            'order' => 'nullable|string|in:asc,desc',
            'segment' => 'nullable|exists:user_segments,slug'
        ]);

        try {
            $perPage = $request->per_page ?? 25;
            $query = User::with(['workspaces', 'gamificationLevel']);

            // Search
            if ($request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->status) {
                $statusMap = ['active' => 1, 'inactive' => 0, 'suspended' => 2];
                $query->where('status', $statusMap[$request->status]);
            }

            // Filter by role
            if ($request->role) {
                $query->where('role', $request->role);
            }

            // Filter by segment
            if ($request->segment) {
                $segment = UserSegment::where('slug', $request->segment)->first();
                if ($segment) {
                    $query->whereHas('segments', function ($q) use ($segment) {
                        $q->where('user_segments.id', $segment->id);
                    });
                }
            }

            // Sort
            $sortField = $request->sort ?? 'created_at';
            $sortOrder = $request->order ?? 'desc';
            $query->orderBy($sortField, $sortOrder);

            $users = $query->paginate($perPage);

            $users->getCollection()->transform(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => $user->status,
                    'role' => $user->role,
                    'workspaces_count' => $user->workspaces->count(),
                    'gamification_level' => $user->gamificationLevel ? $user->gamificationLevel->level : null,
                    'total_xp' => $user->gamificationLevel ? $user->gamificationLevel->total_xp : 0,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'email_verified_at' => $user->email_verified_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total(),
                        'last_page' => $users->lastPage(),
                        'from' => $users->firstItem(),
                        'to' => $users->lastItem()
                    ],
                    'filters' => [
                        'search' => $request->search,
                        'status' => $request->status,
                        'role' => $request->role,
                        'segment' => $request->segment
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('User management index failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load users'
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $user = User::with(['workspaces', 'gamificationLevel', 'gamificationAchievements'])
                        ->findOrFail($id);

            $userDetails = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'role' => $user->role,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'workspaces' => $user->workspaces->map(function ($workspace) {
                    return [
                        'id' => $workspace->id,
                        'name' => $workspace->name,
                        'is_primary' => $workspace->is_primary,
                        'created_at' => $workspace->created_at
                    ];
                }),
                'gamification' => [
                    'level' => $user->gamificationLevel ? $user->gamificationLevel->level : 1,
                    'total_xp' => $user->gamificationLevel ? $user->gamificationLevel->total_xp : 0,
                    'achievements_count' => $user->gamificationAchievements()->where('completed', true)->count(),
                    'tier' => $user->gamificationLevel ? $user->gamificationLevel->level_tier : 'Bronze'
                ],
                'statistics' => [
                    'workspaces_count' => $user->workspaces->count(),
                    'login_count' => $user->gamificationXpEvents()->where('event_type', 'login')->count(),
                    'last_login' => $user->gamificationXpEvents()->where('event_type', 'login')->latest()->first()?->created_at,
                    'account_age_days' => $user->created_at->diffInDays(now())
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $userDetails
            ]);

        } catch (\Exception $e) {
            Log::error('User show failed', [
                'error' => $e->getMessage(),
                'user_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load user details'
            ], 500);
        }
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'mapping' => 'required|array',
            'options' => 'nullable|array'
        ]);

        try {
            $adminUser = $request->user();
            
            // Create bulk operation record
            $bulkOperation = BulkOperation::create([
                'admin_user_id' => $adminUser->id,
                'operation_type' => 'import',
                'entity_type' => 'users',
                'parameters' => [
                    'file_name' => $request->file('file')->getClientOriginalName(),
                    'mapping' => $request->mapping,
                    'options' => $request->options ?? []
                ],
                'status' => 'pending'
            ]);

            // Process file in background (simplified for demo)
            $this->processUserImport($bulkOperation, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Import started successfully',
                'data' => [
                    'operation_id' => $bulkOperation->id,
                    'status' => $bulkOperation->status
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk user import failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start import'
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'updates' => 'required|array',
            'updates.status' => 'nullable|integer|in:0,1,2',
            'updates.role' => 'nullable|string|max:255'
        ]);

        try {
            $adminUser = $request->user();
            
            // Create bulk operation record
            $bulkOperation = BulkOperation::create([
                'admin_user_id' => $adminUser->id,
                'operation_type' => 'update',
                'entity_type' => 'users',
                'parameters' => [
                    'user_ids' => $request->user_ids,
                    'updates' => $request->updates
                ],
                'status' => 'processing',
                'total_records' => count($request->user_ids),
                'started_at' => now()
            ]);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($request->user_ids as $userId) {
                try {
                    $user = User::findOrFail($userId);
                    $oldValues = $user->only(array_keys($request->updates));
                    
                    $user->update($request->updates);
                    
                    // Log the activity
                    $adminUser->logActivity('bulk_update_user', 'User', $userId, $oldValues, $request->updates);
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Update bulk operation
            $bulkOperation->update([
                'status' => $errorCount > 0 ? 'completed_with_errors' : 'completed',
                'processed_records' => $successCount + $errorCount,
                'success_records' => $successCount,
                'failed_records' => $errorCount,
                'errors' => $errors,
                'completed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Updated {$successCount} users successfully",
                'data' => [
                    'operation_id' => $bulkOperation->id,
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk user update failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update users'
            ], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'confirm' => 'required|boolean|accepted'
        ]);

        try {
            $adminUser = $request->user();
            
            // Create bulk operation record
            $bulkOperation = BulkOperation::create([
                'admin_user_id' => $adminUser->id,
                'operation_type' => 'delete',
                'entity_type' => 'users',
                'parameters' => [
                    'user_ids' => $request->user_ids
                ],
                'status' => 'processing',
                'total_records' => count($request->user_ids),
                'started_at' => now()
            ]);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($request->user_ids as $userId) {
                try {
                    $user = User::findOrFail($userId);
                    
                    // Log the activity before deletion
                    $adminUser->logActivity('bulk_delete_user', 'User', $userId, $user->toArray(), null);
                    
                    $user->delete();
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // Update bulk operation
            $bulkOperation->update([
                'status' => $errorCount > 0 ? 'completed_with_errors' : 'completed',
                'processed_records' => $successCount + $errorCount,
                'success_records' => $successCount,
                'failed_records' => $errorCount,
                'errors' => $errors,
                'completed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Deleted {$successCount} users successfully",
                'data' => [
                    'operation_id' => $bulkOperation->id,
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'errors' => $errors
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk user delete failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete users'
            ], 500);
        }
    }

    private function processUserImport(BulkOperation $operation, $file)
    {
        // Simplified import process - in production, this would be queued
        try {
            $operation->update(['status' => 'processing', 'started_at' => now()]);
            
            // Mock processing for demo
            sleep(1);
            
            $operation->update([
                'status' => 'completed',
                'processed_records' => 100,
                'success_records' => 95,
                'failed_records' => 5,
                'completed_at' => now()
            ]);
            
        } catch (\Exception $e) {
            $operation->update([
                'status' => 'failed',
                'errors' => ['error' => $e->getMessage()],
                'completed_at' => now()
            ]);
        }
    }

    public function getStatistics(Request $request)
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('status', 1)->count(),
                'inactive_users' => User::where('status', 0)->count(),
                'suspended_users' => User::where('status', 2)->count(),
                'verified_users' => User::whereNotNull('email_verified_at')->count(),
                'unverified_users' => User::whereNull('email_verified_at')->count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'new_users_this_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
                'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
                'growth_rate' => $this->calculateGrowthRate(),
                'role_distribution' => User::select('role', DB::raw('COUNT(*) as count'))
                                          ->groupBy('role')
                                          ->get()
                                          ->pluck('count', 'role'),
                'registration_trend' => $this->getRegistrationTrend()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('User statistics failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    private function calculateGrowthRate(): float
    {
        $currentMonth = User::whereMonth('created_at', now()->month)
                           ->whereYear('created_at', now()->year)
                           ->count();
        
        $previousMonth = User::whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year)
                            ->count();
        
        if ($previousMonth === 0) {
            return $currentMonth > 0 ? 100 : 0;
        }
        
        return (($currentMonth - $previousMonth) / $previousMonth) * 100;
    }

    private function getRegistrationTrend(): array
    {
        $data = [];
        $startDate = now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'count' => User::whereDate('created_at', $date)->count()
            ];
        }
        
        return $data;
    }
}