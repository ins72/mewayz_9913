<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use App\Models\Admin\EnvironmentVariable;
use App\Models\Admin\SystemSetting;

class EnvironmentController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'group' => 'nullable|string',
            'show_sensitive' => 'nullable|boolean'
        ]);

        try {
            $query = EnvironmentVariable::query();

            if ($request->group) {
                $query->where('group', $request->group);
            }

            $variables = $query->orderBy('group')->orderBy('key')->get();

            // Filter sensitive variables unless explicitly requested
            if (!$request->show_sensitive) {
                $variables = $variables->map(function ($variable) {
                    if ($variable->is_sensitive) {
                        $variable->value = '***********';
                    }
                    return $variable;
                });
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'variables' => $variables,
                    'groups' => EnvironmentVariable::getGroups(),
                    'total_count' => $variables->count(),
                    'sensitive_count' => $variables->where('is_sensitive', true)->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Environment variables index failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load environment variables'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:environment_variables,key',
            'value' => 'required|string',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'is_sensitive' => 'nullable|boolean',
            'requires_restart' => 'nullable|boolean'
        ]);

        try {
            $variableData = $request->validated();
            $variableData['group'] = $variableData['group'] ?? 'general';
            $variableData['is_encrypted'] = $variableData['is_sensitive'] ?? false;

            $variable = EnvironmentVariable::create($variableData);

            // Log the activity
            $request->user()->logActivity('create_environment_variable', 'EnvironmentVariable', $variable->id, null, [
                'key' => $variable->key,
                'group' => $variable->group,
                'is_sensitive' => $variable->is_sensitive
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Environment variable created successfully',
                'data' => $variable,
                'restart_required' => $variable->requires_restart
            ]);

        } catch (\Exception $e) {
            Log::error('Create environment variable failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create environment variable'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'value' => 'required|string',
            'description' => 'nullable|string|max:1000',
            'is_sensitive' => 'nullable|boolean',
            'requires_restart' => 'nullable|boolean'
        ]);

        try {
            $variable = EnvironmentVariable::findOrFail($id);
            $oldValue = $variable->getDisplayValue();

            $variable->update($request->validated());

            // Log the activity (don't log actual values for sensitive variables)
            $request->user()->logActivity('update_environment_variable', 'EnvironmentVariable', $variable->id, 
                ['value' => $variable->is_sensitive ? '***' : $oldValue], 
                ['value' => $variable->is_sensitive ? '***' : $request->value]
            );

            return response()->json([
                'success' => true,
                'message' => 'Environment variable updated successfully',
                'data' => $variable,
                'restart_required' => $variable->requires_restart
            ]);

        } catch (\Exception $e) {
            Log::error('Update environment variable failed', [
                'error' => $e->getMessage(),
                'variable_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update environment variable'
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $variable = EnvironmentVariable::findOrFail($id);
            $variableData = [
                'key' => $variable->key,
                'group' => $variable->group,
                'is_sensitive' => $variable->is_sensitive
            ];

            $variable->delete();

            // Log the activity
            $request->user()->logActivity('delete_environment_variable', 'EnvironmentVariable', $id, $variableData, null);

            return response()->json([
                'success' => true,
                'message' => 'Environment variable deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete environment variable failed', [
                'error' => $e->getMessage(),
                'variable_id' => $id,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete environment variable'
            ], 500);
        }
    }

    public function syncFromEnv(Request $request)
    {
        try {
            $results = EnvironmentVariable::syncFromEnvFile();

            // Log the activity
            $request->user()->logActivity('sync_environment_variables', 'EnvironmentVariable', null, null, $results);

            return response()->json([
                'success' => true,
                'message' => "Synced {$results['synced']} variables from .env file",
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Sync environment variables failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync environment variables'
            ], 500);
        }
    }

    public function writeToEnv(Request $request)
    {
        $request->validate([
            'backup_current' => 'nullable|boolean'
        ]);

        try {
            // Create backup if requested
            if ($request->backup_current) {
                $backupPath = base_path('.env.backup.' . now()->format('Y-m-d-H-i-s'));
                if (file_exists(base_path('.env'))) {
                    copy(base_path('.env'), $backupPath);
                }
            }

            $success = EnvironmentVariable::writeToEnvFile();

            if (!$success) {
                throw new \Exception('Failed to write to .env file');
            }

            // Clear configuration cache
            Artisan::call('config:clear');
            Cache::flush();

            // Log the activity
            $request->user()->logActivity('write_environment_file', 'EnvironmentVariable', null, null, [
                'backup_created' => $request->backup_current ?? false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Environment file updated successfully',
                'restart_required' => true
            ]);

        } catch (\Exception $e) {
            Log::error('Write environment file failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update environment file'
            ], 500);
        }
    }

    public function getSystemSettings(Request $request)
    {
        $request->validate([
            'group' => 'nullable|string'
        ]);

        try {
            $query = SystemSetting::query();

            if ($request->group) {
                $query->where('group', $request->group);
            }

            $settings = $query->orderBy('group')->orderBy('key')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'settings' => $settings,
                    'groups' => $this->getSystemSettingGroups()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('System settings failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load system settings'
            ], 500);
        }
    }

    public function updateSystemSetting(Request $request, $key)
    {
        $request->validate([
            'value' => 'required',
            'type' => 'nullable|string|in:string,integer,boolean,float,json,array',
            'description' => 'nullable|string|max:1000'
        ]);

        try {
            $setting = SystemSetting::where('key', $key)->first();
            
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found'
                ], 404);
            }

            $oldValue = $setting->getValue();
            $setting->setValue($request->value);

            if ($request->type) {
                $setting->type = $request->type;
            }

            if ($request->description) {
                $setting->description = $request->description;
            }

            $setting->save();

            // Log the activity
            $request->user()->logActivity('update_system_setting', 'SystemSetting', $setting->id, 
                ['value' => $oldValue], 
                ['value' => $request->value]
            );

            return response()->json([
                'success' => true,
                'message' => 'System setting updated successfully',
                'data' => $setting
            ]);

        } catch (\Exception $e) {
            Log::error('Update system setting failed', [
                'error' => $e->getMessage(),
                'setting_key' => $key,
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update system setting'
            ], 500);
        }
    }

    public function getSystemInfo(Request $request)
    {
        try {
            $systemInfo = [
                'server' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'environment' => app()->environment(),
                    'timezone' => config('app.timezone'),
                    'locale' => config('app.locale'),
                    'debug_mode' => config('app.debug'),
                    'maintenance_mode' => app()->isDownForMaintenance(),
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
                ],
                'database' => [
                    'connection' => config('database.default'),
                    'driver' => config('database.connections.' . config('database.default') . '.driver'),
                    'host' => config('database.connections.' . config('database.default') . '.host'),
                    'port' => config('database.connections.' . config('database.default') . '.port'),
                    'database' => config('database.connections.' . config('database.default') . '.database')
                ],
                'cache' => [
                    'driver' => config('cache.default'),
                    'stores' => array_keys(config('cache.stores', []))
                ],
                'queue' => [
                    'driver' => config('queue.default'),
                    'connections' => array_keys(config('queue.connections', []))
                ],
                'mail' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'from' => config('mail.from.address')
                ],
                'storage' => [
                    'default' => config('filesystems.default'),
                    'disks' => array_keys(config('filesystems.disks', []))
                ],
                'system' => [
                    'os' => PHP_OS,
                    'memory_limit' => ini_get('memory_limit'),
                    'max_execution_time' => ini_get('max_execution_time'),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size')
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $systemInfo
            ]);

        } catch (\Exception $e) {
            Log::error('System info failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load system information'
            ], 500);
        }
    }

    public function clearCache(Request $request)
    {
        $request->validate([
            'types' => 'required|array',
            'types.*' => 'string|in:config,route,view,cache,all'
        ]);

        try {
            $results = [];

            foreach ($request->types as $type) {
                switch ($type) {
                    case 'config':
                        Artisan::call('config:clear');
                        $results[] = 'Configuration cache cleared';
                        break;
                    case 'route':
                        Artisan::call('route:clear');
                        $results[] = 'Route cache cleared';
                        break;
                    case 'view':
                        Artisan::call('view:clear');
                        $results[] = 'View cache cleared';
                        break;
                    case 'cache':
                        Cache::flush();
                        $results[] = 'Application cache cleared';
                        break;
                    case 'all':
                        Artisan::call('optimize:clear');
                        $results[] = 'All caches cleared';
                        break;
                }
            }

            // Log the activity
            $request->user()->logActivity('clear_cache', 'System', null, null, ['types' => $request->types]);

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Clear cache failed', [
                'error' => $e->getMessage(),
                'admin_user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    private function getSystemSettingGroups(): array
    {
        return [
            'general' => 'General Settings',
            'email' => 'Email Settings',
            'notification' => 'Notification Settings',
            'security' => 'Security Settings',
            'feature' => 'Feature Settings',
            'integration' => 'Integration Settings',
            'analytics' => 'Analytics Settings'
        ];
    }
}