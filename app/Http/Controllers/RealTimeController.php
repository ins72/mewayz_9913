<?php

namespace App\Http\Controllers;

use App\Models\RealTimeSession;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Pusher\Pusher;

class RealTimeController extends Controller
{
    private $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            config('broadcasting.connections.pusher.options')
        );
    }

    /**
     * Start a real-time collaboration session
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'session_type' => 'required|in:collaboration,live_editing,chat,presentation,screen_share',
            'channel_name' => 'required|string|max:255',
            'permissions' => 'nullable|array'
        ]);

        $workspace = Workspace::findOrFail($request->workspace_id);

        // Check if user has access to workspace
        if (!$workspace->hasUser(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session = RealTimeSession::create([
            'workspace_id' => $request->workspace_id,
            'user_id' => Auth::id(),
            'session_id' => Str::uuid(),
            'channel_name' => $request->channel_name,
            'session_type' => $request->session_type,
            'status' => 'active',
            'participants' => [Auth::id()],
            'permissions' => $request->permissions ?? [],
            'session_data' => [],
            'started_at' => now()
        ]);

        // Broadcast session start to workspace members
        $this->pusher->trigger(
            "workspace.{$workspace->id}",
            'session.started',
            [
                'session_id' => $session->session_id,
                'type' => $session->session_type,
                'channel' => $session->channel_name,
                'started_by' => Auth::user()->name,
                'started_at' => $session->started_at
            ]
        );

        return response()->json([
            'session' => $session,
            'channel' => $session->channel_name
        ]);
    }

    /**
     * Join a real-time session
     */
    public function joinSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Check if user has access to workspace
        if (!$session->workspace->hasUser(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Add user to participants
        $participants = $session->participants ?? [];
        if (!in_array(Auth::id(), $participants)) {
            $participants[] = Auth::id();
            $session->update(['participants' => $participants]);
        }

        // Broadcast user joined
        $this->pusher->trigger(
            $session->channel_name,
            'user.joined',
            [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'joined_at' => now()
            ]
        );

        return response()->json([
            'session' => $session,
            'participants' => $session->getParticipantDetails()
        ]);
    }

    /**
     * Leave a real-time session
     */
    public function leaveSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Remove user from participants
        $participants = $session->participants ?? [];
        if (($key = array_search(Auth::id(), $participants)) !== false) {
            unset($participants[$key]);
            $participants = array_values($participants);
            $session->update(['participants' => $participants]);
        }

        // Broadcast user left
        $this->pusher->trigger(
            $session->channel_name,
            'user.left',
            [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'left_at' => now()
            ]
        );

        // End session if no participants left
        if (empty($participants)) {
            $session->update([
                'status' => 'ended',
                'ended_at' => now()
            ]);

            $this->pusher->trigger(
                $session->channel_name,
                'session.ended',
                [
                    'session_id' => $session->session_id,
                    'ended_at' => now()
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Send real-time message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id',
            'message' => 'required|string|max:1000',
            'message_type' => 'required|in:text,system,file,action'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Check if user is participant
        if (!in_array(Auth::id(), $session->participants ?? [])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = [
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'message' => $request->message,
            'message_type' => $request->message_type,
            'timestamp' => now(),
            'metadata' => $request->metadata ?? []
        ];

        // Broadcast message to session participants
        $this->pusher->trigger(
            $session->channel_name,
            'message.new',
            $message
        );

        // Store message in session data
        $sessionData = $session->session_data ?? [];
        $sessionData['messages'][] = $message;
        $session->update(['session_data' => $sessionData]);

        return response()->json(['message' => $message]);
    }

    /**
     * Send real-time data update
     */
    public function sendDataUpdate(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id',
            'data_type' => 'required|string',
            'data' => 'required|array',
            'operation' => 'required|in:create,update,delete'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Check if user is participant
        if (!in_array(Auth::id(), $session->participants ?? [])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $update = [
            'id' => Str::uuid(),
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'data_type' => $request->data_type,
            'operation' => $request->operation,
            'data' => $request->data,
            'timestamp' => now()
        ];

        // Broadcast data update to session participants
        $this->pusher->trigger(
            $session->channel_name,
            'data.update',
            $update
        );

        // Store update in session data
        $sessionData = $session->session_data ?? [];
        $sessionData['updates'][] = $update;
        $session->update(['session_data' => $sessionData]);

        return response()->json(['update' => $update]);
    }

    /**
     * Send cursor position update
     */
    public function sendCursorUpdate(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id',
            'x' => 'required|numeric',
            'y' => 'required|numeric',
            'element_id' => 'nullable|string'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Check if user is participant
        if (!in_array(Auth::id(), $session->participants ?? [])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cursorUpdate = [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name,
            'x' => $request->x,
            'y' => $request->y,
            'element_id' => $request->element_id,
            'timestamp' => now()
        ];

        // Broadcast cursor update to session participants
        $this->pusher->trigger(
            $session->channel_name,
            'cursor.update',
            $cursorUpdate
        );

        return response()->json(['success' => true]);
    }

    /**
     * Get active sessions for workspace
     */
    public function getActiveSessions(Request $request)
    {
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id'
        ]);

        $workspace = Workspace::findOrFail($request->workspace_id);

        // Check if user has access to workspace
        if (!$workspace->hasUser(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $sessions = RealTimeSession::where('workspace_id', $request->workspace_id)
            ->where('status', 'active')
            ->with('user')
            ->get()
            ->map(function ($session) {
                return [
                    'session_id' => $session->session_id,
                    'channel_name' => $session->channel_name,
                    'session_type' => $session->session_type,
                    'started_by' => $session->user->name,
                    'started_at' => $session->started_at,
                    'participant_count' => count($session->participants ?? []),
                    'participants' => $session->getParticipantDetails()
                ];
            });

        return response()->json(['sessions' => $sessions]);
    }

    /**
     * Get session history
     */
    public function getSessionHistory(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Check if user has access to workspace
        if (!$session->workspace->hasUser(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $history = [
            'session_id' => $session->session_id,
            'session_type' => $session->session_type,
            'started_at' => $session->started_at,
            'ended_at' => $session->ended_at,
            'participants' => $session->getParticipantDetails(),
            'messages' => $session->session_data['messages'] ?? [],
            'updates' => $session->session_data['updates'] ?? []
        ];

        return response()->json(['history' => $history]);
    }

    /**
     * End a session
     */
    public function endSession(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:real_time_sessions,session_id'
        ]);

        $session = RealTimeSession::where('session_id', $request->session_id)->firstOrFail();

        // Check if user is the session owner or has admin permissions
        if ($session->user_id !== Auth::id() && !$session->workspace->isAdmin(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->update([
            'status' => 'ended',
            'ended_at' => now()
        ]);

        // Broadcast session ended
        $this->pusher->trigger(
            $session->channel_name,
            'session.ended',
            [
                'session_id' => $session->session_id,
                'ended_by' => Auth::user()->name,
                'ended_at' => now()
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Authenticate user for Pusher
     */
    public function authenticateUser(Request $request)
    {
        $socketId = $request->socket_id;
        $channelName = $request->channel_name;

        // Extract workspace ID from channel name
        if (preg_match('/^workspace\.(\d+)/', $channelName, $matches)) {
            $workspaceId = $matches[1];
            $workspace = Workspace::find($workspaceId);

            if ($workspace && $workspace->hasUser(Auth::id())) {
                $userData = [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email
                ];

                $auth = $this->pusher->authenticateUser($socketId, $userData);
                return response($auth);
            }
        }

        return response('Unauthorized', 403);
    }

    /**
     * Authorize user for private channel
     */
    public function authorizeChannel(Request $request)
    {
        $socketId = $request->socket_id;
        $channelName = $request->channel_name;

        // Extract workspace ID from channel name
        if (preg_match('/^private-workspace\.(\d+)/', $channelName, $matches)) {
            $workspaceId = $matches[1];
            $workspace = Workspace::find($workspaceId);

            if ($workspace && $workspace->hasUser(Auth::id())) {
                $auth = $this->pusher->authorizeChannel($channelName, $socketId);
                return response($auth);
            }
        }

        return response('Unauthorized', 403);
    }
}