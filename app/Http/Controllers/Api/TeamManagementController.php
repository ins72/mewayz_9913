<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceUser;
use App\Models\Team;
use App\Models\Department;
use App\Models\Role;
use App\Models\Permission;
use App\Models\AuditLog;
use App\Models\TimeTracking;
use App\Models\PerformanceMetric;
use App\Models\ApprovalWorkflow;
use App\Services\TeamManagementService;
use App\Services\NotificationService;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TeamManagementController extends Controller
{
    protected $teamService;
    protected $notificationService;
    protected $analyticsService;

    public function __construct(
        TeamManagementService $teamService,
        NotificationService $notificationService,
        AnalyticsService $analyticsService
    ) {
        $this->teamService = $teamService;
        $this->notificationService = $notificationService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get team structure
     */
    public function getTeamStructure(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'error' => 'Workspace not found'
                ], 404);
            }

            $structure = $this->teamService->getTeamStructure($workspace);

            return response()->json([
                'success' => true,
                'data' => $structure,
                'message' => 'Team structure retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve team structure',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create department
     */
    public function createDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_department_id' => 'nullable|uuid|exists:departments,id',
            'manager_id' => 'nullable|uuid|exists:users,id',
            'budget' => 'nullable|numeric|min:0',
            'settings' => 'nullable|array'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$this->teamService->canManageDepartments($user, $workspace)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            $department = Department::create([
                'workspace_id' => $workspace->id,
                'name' => $request->name,
                'description' => $request->description,
                'parent_department_id' => $request->parent_department_id,
                'manager_id' => $request->manager_id,
                'budget' => $request->budget,
                'settings' => $request->settings ?? [],
                'created_by' => $user->id
            ]);

            // Create audit log
            $this->createAuditLog($user, $workspace, 'department_created', [
                'department_id' => $department->id,
                'department_name' => $department->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $department->load(['manager', 'parent', 'children', 'users']),
                'message' => 'Department created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create department',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permission templates
     */
    public function getPermissionTemplates(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            $templates = $this->teamService->getPermissionTemplates($workspace);

            return response()->json([
                'success' => true,
                'data' => $templates,
                'message' => 'Permission templates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve permission templates',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create role with permissions
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
            'template_id' => 'nullable|uuid|exists:role_templates,id',
            'level' => 'required|integer|min:1|max:10'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$this->teamService->canManageRoles($user, $workspace)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            DB::beginTransaction();

            $role = Role::create([
                'workspace_id' => $workspace->id,
                'name' => $request->name,
                'description' => $request->description,
                'level' => $request->level,
                'template_id' => $request->template_id,
                'created_by' => $user->id
            ]);

            // Attach permissions
            $permissions = Permission::whereIn('name', $request->permissions)->get();
            $role->permissions()->sync($permissions->pluck('id'));

            DB::commit();

            // Create audit log
            $this->createAuditLog($user, $workspace, 'role_created', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'permissions' => $request->permissions
            ]);

            return response()->json([
                'success' => true,
                'data' => $role->load('permissions'),
                'message' => 'Role created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to create role',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get audit logs
     */
    public function getAuditLogs(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'user_id' => 'nullable|uuid|exists:users,id',
            'action' => 'nullable|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$this->teamService->canViewAuditLogs($user, $workspace)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            $query = AuditLog::where('workspace_id', $workspace->id)
                ->with(['user', 'targetUser'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->action) {
                $query->where('action', $request->action);
            }

            $logs = $query->paginate($request->per_page ?? 20);

            return response()->json([
                'success' => true,
                'data' => $logs,
                'message' => 'Audit logs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve audit logs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start time tracking
     */
    public function startTimeTracking(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|uuid|exists:projects,id',
            'task_id' => 'nullable|uuid|exists:tasks,id',
            'description' => 'nullable|string|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            // Check if user already has active time tracking
            $activeTracking = TimeTracking::where('user_id', $user->id)
                ->where('workspace_id', $workspace->id)
                ->whereNull('ended_at')
                ->first();

            if ($activeTracking) {
                return response()->json([
                    'success' => false,
                    'error' => 'Active time tracking session exists',
                    'data' => $activeTracking
                ], 400);
            }

            $timeTracking = TimeTracking::create([
                'user_id' => $user->id,
                'workspace_id' => $workspace->id,
                'project_id' => $request->project_id,
                'task_id' => $request->task_id,
                'description' => $request->description,
                'tags' => $request->tags ?? [],
                'started_at' => now(),
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'data' => $timeTracking,
                'message' => 'Time tracking started successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to start time tracking',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stop time tracking
     */
    public function stopTimeTracking(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|uuid|exists:time_trackings,id',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            $timeTracking = TimeTracking::where('id', $request->tracking_id)
                ->where('user_id', $user->id)
                ->where('workspace_id', $workspace->id)
                ->whereNull('ended_at')
                ->first();

            if (!$timeTracking) {
                return response()->json([
                    'success' => false,
                    'error' => 'Active time tracking session not found'
                ], 404);
            }

            $endTime = now();
            $duration = $endTime->diffInMinutes($timeTracking->started_at);

            $timeTracking->update([
                'ended_at' => $endTime,
                'duration_minutes' => $duration,
                'notes' => $request->notes,
                'status' => 'completed'
            ]);

            return response()->json([
                'success' => true,
                'data' => $timeTracking,
                'message' => 'Time tracking stopped successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to stop time tracking',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|uuid|exists:users,id',
            'department_id' => 'nullable|uuid|exists:departments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'metric_type' => 'nullable|string|in:productivity,quality,collaboration,growth'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            $metrics = $this->teamService->getPerformanceMetrics($workspace, [
                'user_id' => $request->user_id,
                'department_id' => $request->department_id,
                'start_date' => $request->start_date ?? now()->subMonth(),
                'end_date' => $request->end_date ?? now(),
                'metric_type' => $request->metric_type
            ]);

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'message' => 'Performance metrics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve performance metrics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create approval workflow
     */
    public function createApprovalWorkflow(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'trigger_type' => 'required|string|in:manual,automatic,scheduled',
            'trigger_conditions' => 'nullable|array',
            'steps' => 'required|array|min:1',
            'steps.*.approver_id' => 'required|uuid|exists:users,id',
            'steps.*.approver_type' => 'required|string|in:user,role,department',
            'steps.*.order' => 'required|integer|min:1',
            'steps.*.required' => 'required|boolean',
            'steps.*.timeout_hours' => 'nullable|integer|min:1'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$this->teamService->canManageWorkflows($user, $workspace)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            DB::beginTransaction();

            $workflow = ApprovalWorkflow::create([
                'workspace_id' => $workspace->id,
                'name' => $request->name,
                'description' => $request->description,
                'trigger_type' => $request->trigger_type,
                'trigger_conditions' => $request->trigger_conditions ?? [],
                'steps' => $request->steps,
                'created_by' => $user->id,
                'status' => 'active'
            ]);

            DB::commit();

            // Create audit log
            $this->createAuditLog($user, $workspace, 'workflow_created', [
                'workflow_id' => $workflow->id,
                'workflow_name' => $workflow->name
            ]);

            return response()->json([
                'success' => true,
                'data' => $workflow,
                'message' => 'Approval workflow created successfully'
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to create approval workflow',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get team analytics
     */
    public function getTeamAnalytics(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department_id' => 'nullable|uuid|exists:departments,id',
            'metrics' => 'nullable|array',
            'metrics.*' => 'string|in:productivity,collaboration,performance,growth,engagement'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            $analytics = $this->teamService->getTeamAnalytics($workspace, [
                'start_date' => $request->start_date ?? now()->subMonth(),
                'end_date' => $request->end_date ?? now(),
                'department_id' => $request->department_id,
                'metrics' => $request->metrics ?? ['productivity', 'collaboration', 'performance']
            ]);

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'message' => 'Team analytics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve team analytics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk invite team members
     */
    public function bulkInviteMembers(Request $request)
    {
        $request->validate([
            'invitations' => 'required|array|min:1|max:50',
            'invitations.*.email' => 'required|email',
            'invitations.*.role' => 'required|string|exists:roles,name',
            'invitations.*.department_id' => 'nullable|uuid|exists:departments,id',
            'invitations.*.custom_message' => 'nullable|string|max:500',
            'send_welcome_email' => 'boolean'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$this->teamService->canInviteMembers($user, $workspace)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            $results = $this->teamService->bulkInviteMembers($workspace, $user, $request->invitations, [
                'send_welcome_email' => $request->send_welcome_email ?? true
            ]);

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Bulk invitation process completed'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send bulk invitations',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create audit log entry
     */
    private function createAuditLog($user, $workspace, $action, $details = [])
    {
        AuditLog::create([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'timestamp' => now()
        ]);
    }

    /**
     * Get team collaboration stats
     */
    public function getCollaborationStats(Request $request)
    {
        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            $stats = $this->teamService->getCollaborationStats($workspace);

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Collaboration stats retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve collaboration stats',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export team data
     */
    public function exportTeamData(Request $request)
    {
        $request->validate([
            'format' => 'required|string|in:csv,xlsx,pdf',
            'data_types' => 'required|array',
            'data_types.*' => 'string|in:members,roles,departments,time_tracking,performance,audit_logs',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        try {
            $user = $request->user();
            $workspace = $request->workspace ?? $user->workspaces()->first();

            if (!$this->teamService->canExportData($user, $workspace)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient permissions'
                ], 403);
            }

            $export = $this->teamService->exportTeamData($workspace, [
                'format' => $request->format,
                'data_types' => $request->data_types,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'requested_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'data' => $export,
                'message' => 'Team data export initiated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to export team data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}