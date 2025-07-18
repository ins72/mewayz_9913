<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCursorMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workspaceId;
    public $userId;
    public $userName;
    public $cursorPosition;
    public $pageUrl;

    /**
     * Create a new event instance.
     */
    public function __construct($workspaceId, $userId, $userName, $cursorPosition, $pageUrl)
    {
        $this->workspaceId = $workspaceId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->cursorPosition = $cursorPosition;
        $this->pageUrl = $pageUrl;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('workspace.' . $this->workspaceId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'cursor_position' => $this->cursorPosition,
            'page_url' => $this->pageUrl,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the name of the broadcast event.
     */
    public function broadcastAs(): string
    {
        return 'cursor.moved';
    }
}