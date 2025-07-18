<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workspaceId;
    public $documentId;
    public $userId;
    public $userName;
    public $changes;
    public $documentType;

    /**
     * Create a new event instance.
     */
    public function __construct($workspaceId, $documentId, $userId, $userName, $changes, $documentType)
    {
        $this->workspaceId = $workspaceId;
        $this->documentId = $documentId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->changes = $changes;
        $this->documentType = $documentType;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('workspace.' . $this->workspaceId),
            new PresenceChannel('document.' . $this->documentId),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->documentId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'changes' => $this->changes,
            'document_type' => $this->documentType,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the name of the broadcast event.
     */
    public function broadcastAs(): string
    {
        return 'document.updated';
    }
}