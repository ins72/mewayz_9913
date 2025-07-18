<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\WorkspaceCollaboration;
use App\Events\UserCursorMoved;
use App\Events\DocumentUpdated;
use App\Events\WorkspaceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WebSocketController extends Controller
{
    /**
     * Track user presence in workspace
     */
    public function joinWorkspace(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        
        if (!$workspaceId) {
            return response()->json(['error' => 'Workspace ID is required'], 400);
        }

        // Store user presence
        $presenceKey = "workspace.{$workspaceId}.users";
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? '',
            'joined_at' => now()->toISOString(),
            'last_activity' => now()->toISOString(),
        ];

        Cache::put($presenceKey . ".{$user->id}", $userData, now()->addMinutes(30));

        // Broadcast user joined event
        broadcast(new WorkspaceCollaboration(
            $workspaceId,
            $user->id,
            $user->name,
            'user_joined',
            $userData
        ));

        // Get current workspace users
        $currentUsers = $this->getWorkspaceUsers($workspaceId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined workspace',
            'data' => [
                'current_users' => $currentUsers,
                'user_data' => $userData,
            ],
        ]);
    }

    /**
     * Handle user leaving workspace
     */
    public function leaveWorkspace(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');

        if (!$workspaceId) {
            return response()->json(['error' => 'Workspace ID is required'], 400);
        }

        // Remove user presence
        $presenceKey = "workspace.{$workspaceId}.users.{$user->id}";
        Cache::forget($presenceKey);

        // Broadcast user left event
        broadcast(new WorkspaceCollaboration(
            $workspaceId,
            $user->id,
            $user->name,
            'user_left',
            ['user_id' => $user->id]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Successfully left workspace',
        ]);
    }

    /**
     * Get current workspace users
     */
    public function getWorkspaceUsers($workspaceId)
    {
        $users = [];
        $pattern = "workspace.{$workspaceId}.users.*";
        
        // Get all cache keys for this workspace
        $cacheKeys = Cache::getMemory()->getKeys();
        
        foreach ($cacheKeys as $key) {
            if (str_contains($key, "workspace.{$workspaceId}.users.")) {
                $userData = Cache::get($key);
                if ($userData) {
                    $users[] = $userData;
                }
            }
        }

        return $users;
    }

    /**
     * Handle cursor movement
     */
    public function updateCursor(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        $cursorPosition = $request->input('cursor_position');
        $pageUrl = $request->input('page_url');

        if (!$workspaceId || !$cursorPosition) {
            return response()->json(['error' => 'Workspace ID and cursor position are required'], 400);
        }

        // Update user activity
        $presenceKey = "workspace.{$workspaceId}.users.{$user->id}";
        $userData = Cache::get($presenceKey);
        if ($userData) {
            $userData['last_activity'] = now()->toISOString();
            Cache::put($presenceKey, $userData, now()->addMinutes(30));
        }

        // Broadcast cursor movement
        broadcast(new UserCursorMoved(
            $workspaceId,
            $user->id,
            $user->name,
            $cursorPosition,
            $pageUrl
        ));

        return response()->json([
            'success' => true,
            'message' => 'Cursor position updated',
        ]);
    }

    /**
     * Handle document updates
     */
    public function updateDocument(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        $documentId = $request->input('document_id');
        $changes = $request->input('changes');
        $documentType = $request->input('document_type', 'text');

        if (!$workspaceId || !$documentId || !$changes) {
            return response()->json(['error' => 'Workspace ID, document ID, and changes are required'], 400);
        }

        // Store document version in cache for conflict resolution
        $versionKey = "document.{$documentId}.version";
        $currentVersion = Cache::get($versionKey, 0);
        $newVersion = $currentVersion + 1;
        Cache::put($versionKey, $newVersion, now()->addHours(24));

        // Broadcast document update
        broadcast(new DocumentUpdated(
            $workspaceId,
            $documentId,
            $user->id,
            $user->name,
            array_merge($changes, ['version' => $newVersion]),
            $documentType
        ));

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => [
                'version' => $newVersion,
            ],
        ]);
    }

    /**
     * Send workspace notification
     */
    public function sendNotification(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        $notificationType = $request->input('notification_type');
        $message = $request->input('message');
        $data = $request->input('data', []);

        if (!$workspaceId || !$notificationType || !$message) {
            return response()->json(['error' => 'Workspace ID, notification type, and message are required'], 400);
        }

        // Broadcast notification
        broadcast(new WorkspaceNotification(
            $workspaceId,
            $user->id,
            $user->name,
            $notificationType,
            $message,
            $data
        ));

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
        ]);
    }

    /**
     * Get workspace activity feed
     */
    public function getActivityFeed(Request $request)
    {
        $workspaceId = $request->input('workspace_id');
        $limit = $request->input('limit', 50);

        if (!$workspaceId) {
            return response()->json(['error' => 'Workspace ID is required'], 400);
        }

        // Get recent activity from cache
        $activityKey = "workspace.{$workspaceId}.activity";
        $activities = Cache::get($activityKey, []);

        // Sort by timestamp and limit
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $activities = array_slice($activities, 0, $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $activities,
                'total' => count($activities),
            ],
        ]);
    }

    /**
     * Start collaborative session
     */
    public function startSession(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        $sessionType = $request->input('session_type', 'general');
        $sessionData = $request->input('session_data', []);

        if (!$workspaceId) {
            return response()->json(['error' => 'Workspace ID is required'], 400);
        }

        $sessionId = uniqid('session_');
        $sessionKey = "workspace.{$workspaceId}.session.{$sessionId}";
        
        $session = [
            'id' => $sessionId,
            'workspace_id' => $workspaceId,
            'type' => $sessionType,
            'host_user_id' => $user->id,
            'host_user_name' => $user->name,
            'participants' => [$user->id],
            'data' => $sessionData,
            'created_at' => now()->toISOString(),
            'status' => 'active',
        ];

        Cache::put($sessionKey, $session, now()->addHours(8));

        // Broadcast session started
        broadcast(new WorkspaceCollaboration(
            $workspaceId,
            $user->id,
            $user->name,
            'session_started',
            $session
        ));

        return response()->json([
            'success' => true,
            'message' => 'Collaborative session started',
            'data' => $session,
        ]);
    }

    /**
     * Join collaborative session
     */
    public function joinSession(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        $sessionId = $request->input('session_id');

        if (!$workspaceId || !$sessionId) {
            return response()->json(['error' => 'Workspace ID and session ID are required'], 400);
        }

        $sessionKey = "workspace.{$workspaceId}.session.{$sessionId}";
        $session = Cache::get($sessionKey);

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Add user to participants
        if (!in_array($user->id, $session['participants'])) {
            $session['participants'][] = $user->id;
            Cache::put($sessionKey, $session, now()->addHours(8));
        }

        // Broadcast user joined session
        broadcast(new WorkspaceCollaboration(
            $workspaceId,
            $user->id,
            $user->name,
            'user_joined_session',
            [
                'session_id' => $sessionId,
                'user_id' => $user->id,
                'user_name' => $user->name,
            ]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined session',
            'data' => $session,
        ]);
    }

    /**
     * End collaborative session
     */
    public function endSession(Request $request)
    {
        $user = $request->user();
        $workspaceId = $request->input('workspace_id');
        $sessionId = $request->input('session_id');

        if (!$workspaceId || !$sessionId) {
            return response()->json(['error' => 'Workspace ID and session ID are required'], 400);
        }

        $sessionKey = "workspace.{$workspaceId}.session.{$sessionId}";
        $session = Cache::get($sessionKey);

        if (!$session) {
            return response()->json(['error' => 'Session not found'], 404);
        }

        // Only host can end session
        if ($session['host_user_id'] !== $user->id) {
            return response()->json(['error' => 'Only session host can end the session'], 403);
        }

        // Update session status
        $session['status'] = 'ended';
        $session['ended_at'] = now()->toISOString();
        Cache::put($sessionKey, $session, now()->addHours(24)); // Keep for history

        // Broadcast session ended
        broadcast(new WorkspaceCollaboration(
            $workspaceId,
            $user->id,
            $user->name,
            'session_ended',
            [
                'session_id' => $sessionId,
                'ended_by' => $user->name,
            ]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Session ended successfully',
            'data' => $session,
        ]);
    }
}