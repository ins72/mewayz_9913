<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Workspace collaboration channels
Broadcast::channel('workspace.{workspaceId}', function ($user, $workspaceId) {
    // Check if user has access to this workspace
    // For now, we'll allow authenticated users - you can add workspace permission logic here
    return $user ? [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->avatar ?? '',
    ] : false;
});

// Document collaboration channels
Broadcast::channel('document.{documentId}', function ($user, $documentId) {
    // Check if user has access to this document
    // For now, we'll allow authenticated users - you can add document permission logic here
    return $user ? [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'avatar' => $user->avatar ?? '',
    ] : false;
});
