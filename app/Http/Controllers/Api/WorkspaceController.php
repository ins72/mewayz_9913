<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $workspaces = Organization::where('user_id', $request->user()->id)
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

        $workspace = Organization::create([
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

    public function show(Organization $workspace)
    {
        // Check if user owns the workspace
        if ($workspace->user_id !== auth()->id()) {
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

        // TODO: Implement team invitation logic
        // This would typically involve creating an invitation record
        // and sending an email to the invited user

        return response()->json([
            'success' => true,
            'message' => 'Team member invited successfully',
        ]);
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

        // TODO: Implement team members retrieval
        // This would return workspace members with their roles

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}