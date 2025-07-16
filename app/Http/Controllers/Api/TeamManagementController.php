<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamInvitation;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeamManagementController extends Controller
{
    /**
     * Get team members and invitations
     */
    public function getTeam()
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            // Get pending invitations
            $pendingInvitations = $workspace->pendingInvitations()->get();
            
            // Get team members (accepted invitations)
            $teamMembers = $workspace->teamInvitations()->accepted()->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'workspace' => $workspace,
                    'owner' => $user,
                    'team_members' => $teamMembers,
                    'pending_invitations' => $pendingInvitations,
                    'team_size' => 1 + $teamMembers->count(),
                    'available_roles' => TeamInvitation::getAvailableRoles(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load team: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Send team invitation
     */
    public function sendInvitation(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'role' => 'required|string|in:member,editor,manager,admin',
                'permissions' => 'sometimes|array',
                'message' => 'sometimes|string|max:500',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            if (!$workspace) {
                return response()->json([
                    'success' => false,
                    'message' => 'No workspace found'
                ], 404);
            }
            
            // Check if user is already invited or is the owner
            if ($request->email === $user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot invite yourself'
                ], 400);
            }
            
            $existingInvitation = $workspace->teamInvitations()
                ->where('email', $request->email)
                ->whereIn('status', ['pending', 'accepted'])
                ->first();
                
            if ($existingInvitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already invited or part of the team'
                ], 400);
            }
            
            // Create invitation
            $invitation = TeamInvitation::create([
                'workspace_id' => $workspace->id,
                'invited_by' => $user->id,
                'email' => $request->email,
                'role' => $request->role,
                'permissions' => $request->permissions ?? [],
                'expires_at' => now()->addDays(7),
            ]);
            
            // TODO: Send invitation email
            // Mail::to($request->email)->send(new TeamInvitationMail($invitation));
            
            return response()->json([
                'success' => true,
                'message' => 'Invitation sent successfully',
                'data' => [
                    'invitation' => $invitation->load('invitedBy'),
                    'invitation_url' => $invitation->getInvitationUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Accept team invitation
     */
    public function acceptInvitation(Request $request, $uuid)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);
            
            $invitation = TeamInvitation::where('uuid', $uuid)
                ->where('token', $request->token)
                ->where('status', 'pending')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired invitation'
                ], 404);
            }
            
            if ($invitation->isExpired()) {
                $invitation->markAsExpired();
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation has expired'
                ], 400);
            }
            
            $user = auth()->user();
            
            // Check if user email matches invitation email
            if ($user->email !== $invitation->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation email does not match your account'
                ], 400);
            }
            
            // Accept the invitation
            $invitation->accept();
            
            // Update user's workspace relationship (if needed)
            // This depends on your user-workspace relationship structure
            
            return response()->json([
                'success' => true,
                'message' => 'Invitation accepted successfully',
                'data' => [
                    'invitation' => $invitation->load('workspace', 'invitedBy'),
                    'workspace' => $invitation->workspace,
                    'role' => $invitation->role,
                    'permissions' => $invitation->permissions,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept invitation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reject team invitation
     */
    public function rejectInvitation(Request $request, $uuid)
    {
        try {
            $request->validate([
                'token' => 'required|string',
            ]);
            
            $invitation = TeamInvitation::where('uuid', $uuid)
                ->where('token', $request->token)
                ->where('status', 'pending')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired invitation'
                ], 404);
            }
            
            $invitation->reject();
            
            return response()->json([
                'success' => true,
                'message' => 'Invitation rejected successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject invitation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Resend invitation
     */
    public function resendInvitation($invitationId)
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            $invitation = $workspace->teamInvitations()
                ->where('id', $invitationId)
                ->where('status', 'pending')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation not found'
                ], 404);
            }
            
            // Extend expiration date
            $invitation->update([
                'expires_at' => now()->addDays(7),
                'token' => Str::random(64), // Generate new token
            ]);
            
            // TODO: Resend invitation email
            // Mail::to($invitation->email)->send(new TeamInvitationMail($invitation));
            
            return response()->json([
                'success' => true,
                'message' => 'Invitation resent successfully',
                'data' => [
                    'invitation' => $invitation,
                    'invitation_url' => $invitation->getInvitationUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend invitation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cancel invitation
     */
    public function cancelInvitation($invitationId)
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            $invitation = $workspace->teamInvitations()
                ->where('id', $invitationId)
                ->where('status', 'pending')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation not found'
                ], 404);
            }
            
            $invitation->update(['status' => 'expired']);
            
            return response()->json([
                'success' => true,
                'message' => 'Invitation cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel invitation: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update team member role
     */
    public function updateMemberRole(Request $request, $invitationId)
    {
        try {
            $request->validate([
                'role' => 'required|string|in:member,editor,manager,admin',
                'permissions' => 'sometimes|array',
            ]);
            
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            $invitation = $workspace->teamInvitations()
                ->where('id', $invitationId)
                ->where('status', 'accepted')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }
            
            $invitation->update([
                'role' => $request->role,
                'permissions' => $request->permissions ?? $invitation->permissions,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Member role updated successfully',
                'data' => [
                    'invitation' => $invitation,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update member role: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove team member
     */
    public function removeMember($invitationId)
    {
        try {
            $user = auth()->user();
            $workspace = $user->workspaces()->first();
            
            $invitation = $workspace->teamInvitations()
                ->where('id', $invitationId)
                ->where('status', 'accepted')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found'
                ], 404);
            }
            
            $invitation->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Team member removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get invitation details (for invitation page)
     */
    public function getInvitationDetails($uuid)
    {
        try {
            $invitation = TeamInvitation::where('uuid', $uuid)
                ->where('status', 'pending')
                ->with('workspace', 'invitedBy')
                ->first();
                
            if (!$invitation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired invitation'
                ], 404);
            }
            
            if ($invitation->isExpired()) {
                $invitation->markAsExpired();
                return response()->json([
                    'success' => false,
                    'message' => 'Invitation has expired'
                ], 400);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'invitation' => $invitation,
                    'workspace' => $invitation->workspace,
                    'invited_by' => $invitation->invitedBy,
                    'role' => $invitation->getRoleDisplayName(),
                    'expires_at' => $invitation->expires_at->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get invitation details: ' . $e->getMessage()
            ], 500);
        }
    }
}
