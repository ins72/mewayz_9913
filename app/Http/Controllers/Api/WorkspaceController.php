<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $workspaces = Workspace::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $workspaces,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $workspace = Workspace::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Workspace created successfully',
            'data' => $workspace,
        ], 201);
    }

    public function show(Request $request, Organization $workspace)
    {
        // Check if user owns the workspace
        if ($workspace->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to workspace',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $workspace,
        ]);
    }

    public function update(Request $request, Organization $workspace)
    {
        // Check if user owns the workspace
        if ($workspace->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to workspace',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $workspace->update($request->only(['name', 'description']));

        return response()->json([
            'success' => true,
            'message' => 'Workspace updated successfully',
            'data' => $workspace,
        ]);
    }

    public function destroy(Organization $workspace)
    {
        // Check if user owns the workspace
        if ($workspace->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to workspace',
            ], 403);
        }

        $workspace->delete();

        return response()->json([
            'success' => true,
            'message' => 'Workspace deleted successfully',
        ]);
    }

    public function inviteTeamMember(Request $request, Organization $workspace)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:admin,editor,viewer',
        ]);

        // Check if user owns the workspace
        if ($workspace->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to workspace',
            ], 403);
        }

        try {
            // Create team invitation record
            $invitation = \App\Models\YenaTeamsInvite::create([
                'organization_id' => $workspace->id,
                'email' => $request->email,
                'role' => $request->role,
                'invited_by' => auth()->id(),
                'token' => \Illuminate\Support\Str::random(64),
                'expires_at' => now()->addDays(7),
                'status' => 'pending'
            ]);

            // Send invitation email
            try {
                // Here you would send an email with the invitation link
                // For now, we'll just log the invitation
                \Illuminate\Support\Facades\Log::info("Team invitation sent", [
                    'workspace_id' => $workspace->id,
                    'email' => $request->email,
                    'role' => $request->role,
                    'token' => $invitation->token
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send invitation email: " . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Team member invited successfully',
                'data' => [
                    'invitation_id' => $invitation->id,
                    'email' => $invitation->email,
                    'role' => $invitation->role,
                    'expires_at' => $invitation->expires_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMembers(Organization $workspace)
    {
        // Check if user owns the workspace
        if ($workspace->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to workspace',
            ], 403);
        }

        try {
            // Get workspace members from teams table
            $members = \App\Models\YenaTeamsUserTable::where('organization_id', $workspace->id)
                ->with('user:id,name,email')
                ->get()
                ->map(function ($member) {
                    return [
                        'id' => $member->user->id,
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                        'role' => $member->role ?? 'member',
                        'joined_at' => $member->created_at,
                        'status' => 'active'
                    ];
                });

            // Add workspace owner
            $owner = [
                'id' => $workspace->user_id,
                'name' => $workspace->user->name ?? 'Owner',
                'email' => $workspace->user->email ?? '',
                'role' => 'owner',
                'joined_at' => $workspace->created_at,
                'status' => 'active'
            ];

            $members->prepend($owner);

            // Get pending invitations
            $pendingInvitations = \App\Models\YenaTeamsInvite::where('organization_id', $workspace->id)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->get()
                ->map(function ($invite) {
                    return [
                        'id' => $invite->id,
                        'email' => $invite->email,
                        'role' => $invite->role,
                        'status' => 'pending',
                        'invited_at' => $invite->created_at,
                        'expires_at' => $invite->expires_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'members' => $members,
                    'pending_invitations' => $pendingInvitations,
                    'total_members' => $members->count(),
                    'total_pending' => $pendingInvitations->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team members: ' . $e->getMessage(),
            ], 500);
        }
    }
}