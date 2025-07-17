<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SSOProvider;
use App\Models\WhiteLabelConfig;
use App\Models\AuditLog;
use App\Models\SecurityEvent;
use App\Models\Department;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class EnterpriseController extends Controller
{
    /**
     * Get SSO providers for workspace
     */
    public function getSSOProviders(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $providers = SSOProvider::where('workspace_id', $workspace->id)
            ->orderBy('name')
            ->get()
            ->map(function ($provider) {
                return [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'provider_type' => $provider->provider_type,
                    'is_active' => $provider->is_active,
                    'entity_id' => $provider->entity_id,
                    'metadata_url' => $provider->metadata_url,
                    'created_at' => $provider->created_at
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $providers
        ]);
    }
    
    /**
     * Create SSO provider
     */
    public function createSSOProvider(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider_type' => 'required|in:saml,oauth,ldap',
            'config' => 'required|array',
            'entity_id' => 'nullable|string',
            'metadata_url' => 'nullable|url',
            'certificate' => 'nullable|string',
            'attribute_mapping' => 'nullable|array'
        ]);
        
        $workspace = $request->user()->workspaces()->first();
        
        $provider = SSOProvider::create([
            'workspace_id' => $workspace->id,
            'name' => $request->name,
            'provider_type' => $request->provider_type,
            'config' => $request->config,
            'entity_id' => $request->entity_id,
            'metadata_url' => $request->metadata_url,
            'certificate' => $request->certificate,
            'attribute_mapping' => $request->attribute_mapping ?? []
        ]);
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'create',
            'resource_type' => 'sso_provider',
            'resource_id' => $provider->id,
            'new_values' => $provider->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $provider,
            'message' => 'SSO provider created successfully'
        ], 201);
    }
    
    /**
     * Update SSO provider
     */
    public function updateSSOProvider(Request $request, string $id): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $provider = SSOProvider::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->firstOrFail();
        
        $oldValues = $provider->toArray();
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'provider_type' => 'sometimes|in:saml,oauth,ldap',
            'config' => 'sometimes|array',
            'entity_id' => 'nullable|string',
            'metadata_url' => 'nullable|url',
            'certificate' => 'nullable|string',
            'attribute_mapping' => 'nullable|array',
            'is_active' => 'sometimes|boolean'
        ]);
        
        $provider->update($request->only([
            'name', 'provider_type', 'config', 'entity_id', 
            'metadata_url', 'certificate', 'attribute_mapping', 'is_active'
        ]));
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'update',
            'resource_type' => 'sso_provider',
            'resource_id' => $provider->id,
            'old_values' => $oldValues,
            'new_values' => $provider->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $provider,
            'message' => 'SSO provider updated successfully'
        ]);
    }
    
    /**
     * Test SSO provider connection
     */
    public function testSSOProvider(Request $request, string $id): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $provider = SSOProvider::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->firstOrFail();
        
        $result = $provider->testConnection();
        
        // Log the test
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'test',
            'resource_type' => 'sso_provider',
            'resource_id' => $provider->id,
            'metadata' => $result
        ]);
        
        return response()->json([
            'success' => $result['success'],
            'data' => $result,
            'message' => $result['message']
        ]);
    }
    
    /**
     * Delete SSO provider
     */
    public function deleteSSOProvider(Request $request, string $id): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $provider = SSOProvider::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->firstOrFail();
        
        $oldValues = $provider->toArray();
        
        $provider->delete();
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'delete',
            'resource_type' => 'sso_provider',
            'resource_id' => $id,
            'old_values' => $oldValues
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'SSO provider deleted successfully'
        ]);
    }
    
    /**
     * Get white label configuration
     */
    public function getWhiteLabelConfig(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $config = WhiteLabelConfig::where('workspace_id', $workspace->id)->first();
        
        return response()->json([
            'success' => true,
            'data' => $config
        ]);
    }
    
    /**
     * Update white label configuration
     */
    public function updateWhiteLabelConfig(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'logo_url' => 'nullable|url',
            'favicon_url' => 'nullable|url',
            'primary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'secondary_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'accent_color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'custom_domain' => 'nullable|string',
            'email_templates' => 'nullable|array',
            'custom_css' => 'nullable|array',
            'custom_js' => 'nullable|array',
            'hide_platform_branding' => 'boolean',
            'custom_login_page' => 'boolean',
            'login_page_config' => 'nullable|array'
        ]);
        
        $config = WhiteLabelConfig::updateOrCreate(
            ['workspace_id' => $workspace->id],
            $request->only([
                'company_name', 'logo_url', 'favicon_url', 'primary_color',
                'secondary_color', 'accent_color', 'custom_domain',
                'email_templates', 'custom_css', 'custom_js',
                'hide_platform_branding', 'custom_login_page', 'login_page_config'
            ])
        );
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'update',
            'resource_type' => 'white_label_config',
            'resource_id' => $config->id,
            'new_values' => $config->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $config,
            'message' => 'White label configuration updated successfully'
        ]);
    }
    
    /**
     * Generate white label CSS
     */
    public function generateWhiteLabelCSS(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $config = WhiteLabelConfig::where('workspace_id', $workspace->id)->first();
        
        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'White label configuration not found'
            ], 404);
        }
        
        $css = $config->generateCSS();
        
        return response()->json([
            'success' => true,
            'data' => ['css' => $css],
            'message' => 'CSS generated successfully'
        ]);
    }
    
    /**
     * Get departments hierarchy
     */
    public function getDepartments(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $departments = Department::where('workspace_id', $workspace->id)
            ->with(['manager', 'parent', 'children'])
            ->whereNull('parent_department_id')
            ->orderBy('name')
            ->get()
            ->map(function ($department) {
                return $this->formatDepartment($department);
            });
        
        return response()->json([
            'success' => true,
            'data' => $departments
        ]);
    }
    
    /**
     * Create department
     */
    public function createDepartment(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_department_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'settings' => 'nullable|array'
        ]);
        
        $department = Department::create([
            'workspace_id' => $workspace->id,
            'name' => $request->name,
            'description' => $request->description,
            'parent_department_id' => $request->parent_department_id,
            'manager_id' => $request->manager_id,
            'budget' => $request->budget ?? 0,
            'settings' => $request->settings ?? [],
            'created_by' => $request->user()->id
        ]);
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'create',
            'resource_type' => 'department',
            'resource_id' => $department->id,
            'new_values' => $department->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $this->formatDepartment($department->load(['manager', 'parent', 'children'])),
            'message' => 'Department created successfully'
        ], 201);
    }
    
    /**
     * Get teams for workspace
     */
    public function getTeams(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $teams = Team::where('workspace_id', $workspace->id)
            ->with(['leader', 'department', 'members'])
            ->orderBy('name')
            ->get()
            ->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'description' => $team->description,
                    'leader' => $team->leader ? [
                        'id' => $team->leader->id,
                        'name' => $team->leader->name,
                        'email' => $team->leader->email
                    ] : null,
                    'department' => $team->department ? [
                        'id' => $team->department->id,
                        'name' => $team->department->name
                    ] : null,
                    'members_count' => $team->members->count(),
                    'status' => $team->status,
                    'created_at' => $team->created_at
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $teams
        ]);
    }
    
    /**
     * Create team
     */
    public function createTeam(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'settings' => 'nullable|array'
        ]);
        
        $team = Team::create([
            'workspace_id' => $workspace->id,
            'name' => $request->name,
            'description' => $request->description,
            'leader_id' => $request->leader_id,
            'department_id' => $request->department_id,
            'settings' => $request->settings ?? [],
            'created_by' => $request->user()->id
        ]);
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'create',
            'resource_type' => 'team',
            'resource_id' => $team->id,
            'new_values' => $team->toArray()
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $team->load(['leader', 'department']),
            'message' => 'Team created successfully'
        ], 201);
    }
    
    /**
     * Get audit logs
     */
    public function getAuditLogs(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $logs = AuditLog::where('workspace_id', $workspace->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $logs->items(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'last_page' => $logs->lastPage()
            ]
        ]);
    }
    
    /**
     * Get security events
     */
    public function getSecurityEvents(Request $request): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $events = SecurityEvent::where('workspace_id', $workspace->id)
            ->with(['user', 'resolver'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
                'last_page' => $events->lastPage()
            ]
        ]);
    }
    
    /**
     * Resolve security event
     */
    public function resolveSecurityEvent(Request $request, string $id): JsonResponse
    {
        $workspace = $request->user()->workspaces()->first();
        
        $event = SecurityEvent::where('id', $id)
            ->where('workspace_id', $workspace->id)
            ->firstOrFail();
        
        $event->resolve($request->user()->id);
        
        // Log the action
        AuditLog::logAction([
            'workspace_id' => $workspace->id,
            'action' => 'resolve',
            'resource_type' => 'security_event',
            'resource_id' => $event->id
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Security event resolved successfully'
        ]);
    }
    
    /**
     * Format department for API response
     */
    private function formatDepartment($department): array
    {
        return [
            'id' => $department->id,
            'name' => $department->name,
            'description' => $department->description,
            'manager' => $department->manager ? [
                'id' => $department->manager->id,
                'name' => $department->manager->name,
                'email' => $department->manager->email
            ] : null,
            'parent' => $department->parent ? [
                'id' => $department->parent->id,
                'name' => $department->parent->name
            ] : null,
            'children' => $department->children->map(function ($child) {
                return $this->formatDepartment($child);
            }),
            'budget' => $department->budget,
            'status' => $department->status,
            'hierarchy_path' => $department->getHierarchyPath(),
            'level' => $department->getLevel(),
            'created_at' => $department->created_at
        ];
    }
}