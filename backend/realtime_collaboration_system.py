# Real-time Collaboration System with WebSocket Support
from fastapi import FastAPI, WebSocket, WebSocketDisconnect, HTTPException, Depends
from fastapi.responses import JSONResponse
from pydantic import BaseModel, Field
from typing import List, Dict, Set, Optional, Any
from datetime import datetime
import json
import uuid
import asyncio
from enum import Enum
import logging

# Set up logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class DocumentType(str, Enum):
    TEXT = "text"
    WHITEBOARD = "whiteboard"
    PRESENTATION = "presentation"
    SPREADSHEET = "spreadsheet"
    CODE = "code"

class CollaborationEvent(str, Enum):
    USER_JOIN = "user_join"
    USER_LEAVE = "user_leave"
    CURSOR_MOVE = "cursor_move"
    TEXT_CHANGE = "text_change"
    CHAT_MESSAGE = "chat_message"
    DOCUMENT_SAVE = "document_save"
    USER_TYPING = "user_typing"
    SELECTION_CHANGE = "selection_change"

class UserPresence(BaseModel):
    user_id: str
    name: str
    avatar: Optional[str] = None
    color: str = "#3b82f6"
    cursor_position: Optional[Dict[str, Any]] = None
    selection: Optional[Dict[str, Any]] = None
    status: str = "active"  # active, idle, typing
    last_seen: datetime = Field(default_factory=datetime.utcnow)

class ChatMessage(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    user_id: str
    user_name: str
    message: str
    timestamp: datetime = Field(default_factory=datetime.utcnow)
    message_type: str = "message"  # message, activity, system

class DocumentChange(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    user_id: str
    change_type: str  # insert, delete, format
    position: int
    content: str
    length: int = 0
    timestamp: datetime = Field(default_factory=datetime.utcnow)

class CollaborationRoom(BaseModel):
    id: str
    document_id: str
    document_type: DocumentType
    title: str
    created_by: str
    created_at: datetime = Field(default_factory=datetime.utcnow)
    is_active: bool = True
    max_participants: int = 50

class CollaborationSession(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    room_id: str
    user_id: str
    joined_at: datetime = Field(default_factory=datetime.utcnow)
    last_activity: datetime = Field(default_factory=datetime.utcnow)

# WebSocket Connection Manager
class ConnectionManager:
    def __init__(self):
        # Store active connections per room
        self.active_connections: Dict[str, Dict[str, WebSocket]] = {}
        # Store user presence per room
        self.room_presence: Dict[str, Dict[str, UserPresence]] = {}
        # Store chat messages per room
        self.room_messages: Dict[str, List[ChatMessage]] = {}
        # Store document changes per room
        self.room_changes: Dict[str, List[DocumentChange]] = {}
        
    async def connect(self, websocket: WebSocket, room_id: str, user_id: str, user_data: dict):
        await websocket.accept()
        
        # Initialize room if not exists
        if room_id not in self.active_connections:
            self.active_connections[room_id] = {}
            self.room_presence[room_id] = {}
            self.room_messages[room_id] = []
            self.room_changes[room_id] = []
            
        # Add connection
        self.active_connections[room_id][user_id] = websocket
        
        # Add user presence
        self.room_presence[room_id][user_id] = UserPresence(
            user_id=user_id,
            name=user_data.get('name', 'Unknown User'),
            avatar=user_data.get('avatar'),
            color=user_data.get('color', '#3b82f6')
        )
        
        # Notify other users that someone joined
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.USER_JOIN,
            "user_id": user_id,
            "user_data": user_data,
            "room_users": len(self.active_connections[room_id]),
            "timestamp": datetime.utcnow().isoformat()
        }, exclude_user=user_id)
        
        # Send current room state to new user
        await websocket.send_json({
            "type": "room_state",
            "users": [presence.dict() for presence in self.room_presence[room_id].values()],
            "messages": [msg.dict() for msg in self.room_messages[room_id][-50:]],  # Last 50 messages
            "changes": [change.dict() for change in self.room_changes[room_id][-100:]],  # Last 100 changes
            "timestamp": datetime.utcnow().isoformat()
        })
        
        logger.info(f"User {user_id} connected to room {room_id}")
        
    async def disconnect(self, room_id: str, user_id: str):
        # Remove connection
        if room_id in self.active_connections and user_id in self.active_connections[room_id]:
            del self.active_connections[room_id][user_id]
            
        # Remove presence
        if room_id in self.room_presence and user_id in self.room_presence[room_id]:
            del self.room_presence[room_id][user_id]
            
        # Clean up empty rooms
        if room_id in self.active_connections and len(self.active_connections[room_id]) == 0:
            del self.active_connections[room_id]
            # Keep messages and changes for a while in case users rejoin
            
        # Notify other users
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.USER_LEAVE,
            "user_id": user_id,
            "room_users": len(self.active_connections.get(room_id, {})),
            "timestamp": datetime.utcnow().isoformat()
        })
        
        logger.info(f"User {user_id} disconnected from room {room_id}")
        
    async def send_personal_message(self, room_id: str, user_id: str, message: dict):
        if room_id in self.active_connections and user_id in self.active_connections[room_id]:
            websocket = self.active_connections[room_id][user_id]
            try:
                await websocket.send_json(message)
            except Exception as e:
                logger.error(f"Error sending message to {user_id}: {e}")
                await self.disconnect(room_id, user_id)
                
    async def broadcast_to_room(self, room_id: str, message: dict, exclude_user: str = None):
        if room_id not in self.active_connections:
            return
            
        disconnected_users = []
        for user_id, websocket in self.active_connections[room_id].items():
            if exclude_user and user_id == exclude_user:
                continue
                
            try:
                await websocket.send_json(message)
            except Exception as e:
                logger.error(f"Error broadcasting to {user_id}: {e}")
                disconnected_users.append(user_id)
                
        # Clean up disconnected users
        for user_id in disconnected_users:
            await self.disconnect(room_id, user_id)
            
    async def handle_message(self, room_id: str, user_id: str, message_data: dict):
        message_type = message_data.get("type")
        
        if message_type == CollaborationEvent.CHAT_MESSAGE:
            await self.handle_chat_message(room_id, user_id, message_data)
        elif message_type == CollaborationEvent.CURSOR_MOVE:
            await self.handle_cursor_move(room_id, user_id, message_data)
        elif message_type == CollaborationEvent.TEXT_CHANGE:
            await self.handle_text_change(room_id, user_id, message_data)
        elif message_type == CollaborationEvent.USER_TYPING:
            await self.handle_user_typing(room_id, user_id, message_data)
        elif message_type == CollaborationEvent.SELECTION_CHANGE:
            await self.handle_selection_change(room_id, user_id, message_data)
        else:
            logger.warning(f"Unknown message type: {message_type}")
            
    async def handle_chat_message(self, room_id: str, user_id: str, message_data: dict):
        if room_id not in self.room_messages:
            self.room_messages[room_id] = []
            
        user_presence = self.room_presence.get(room_id, {}).get(user_id)
        if not user_presence:
            return
            
        chat_message = ChatMessage(
            user_id=user_id,
            user_name=user_presence.name,
            message=message_data.get("message", ""),
            message_type=message_data.get("message_type", "message")
        )
        
        self.room_messages[room_id].append(chat_message)
        
        # Keep only last 200 messages
        if len(self.room_messages[room_id]) > 200:
            self.room_messages[room_id] = self.room_messages[room_id][-200:]
            
        # Broadcast to all users in room
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.CHAT_MESSAGE,
            "message": chat_message.dict(),
            "timestamp": datetime.utcnow().isoformat()
        })
        
    async def handle_cursor_move(self, room_id: str, user_id: str, message_data: dict):
        if room_id in self.room_presence and user_id in self.room_presence[room_id]:
            self.room_presence[room_id][user_id].cursor_position = message_data.get("position")
            self.room_presence[room_id][user_id].last_seen = datetime.utcnow()
            
        # Broadcast cursor position to other users
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.CURSOR_MOVE,
            "user_id": user_id,
            "position": message_data.get("position"),
            "timestamp": datetime.utcnow().isoformat()
        }, exclude_user=user_id)
        
    async def handle_text_change(self, room_id: str, user_id: str, message_data: dict):
        if room_id not in self.room_changes:
            self.room_changes[room_id] = []
            
        change = DocumentChange(
            user_id=user_id,
            change_type=message_data.get("change_type", "insert"),
            position=message_data.get("position", 0),
            content=message_data.get("content", ""),
            length=message_data.get("length", 0)
        )
        
        self.room_changes[room_id].append(change)
        
        # Keep only last 1000 changes
        if len(self.room_changes[room_id]) > 1000:
            self.room_changes[room_id] = self.room_changes[room_id][-1000:]
            
        # Broadcast change to all users
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.TEXT_CHANGE,
            "change": change.dict(),
            "timestamp": datetime.utcnow().isoformat()
        }, exclude_user=user_id)
        
    async def handle_user_typing(self, room_id: str, user_id: str, message_data: dict):
        if room_id in self.room_presence and user_id in self.room_presence[room_id]:
            self.room_presence[room_id][user_id].status = "typing" if message_data.get("is_typing") else "active"
            
        # Broadcast typing status
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.USER_TYPING,
            "user_id": user_id,
            "is_typing": message_data.get("is_typing", False),
            "timestamp": datetime.utcnow().isoformat()
        }, exclude_user=user_id)
        
    async def handle_selection_change(self, room_id: str, user_id: str, message_data: dict):
        if room_id in self.room_presence and user_id in self.room_presence[room_id]:
            self.room_presence[room_id][user_id].selection = message_data.get("selection")
            
        # Broadcast selection to other users
        await self.broadcast_to_room(room_id, {
            "type": CollaborationEvent.SELECTION_CHANGE,
            "user_id": user_id,
            "selection": message_data.get("selection"),
            "timestamp": datetime.utcnow().isoformat()
        }, exclude_user=user_id)
        
    def get_room_stats(self, room_id: str) -> Dict:
        return {
            "active_users": len(self.active_connections.get(room_id, {})),
            "total_messages": len(self.room_messages.get(room_id, [])),
            "total_changes": len(self.room_changes.get(room_id, [])),
            "users": [presence.dict() for presence in self.room_presence.get(room_id, {}).values()]
        }

# Global connection manager
connection_manager = ConnectionManager()

# API Models
class CreateRoomRequest(BaseModel):
    document_id: str
    document_type: DocumentType
    title: str
    max_participants: int = 50

class JoinRoomRequest(BaseModel):
    room_id: str
    user_name: str
    user_avatar: Optional[str] = None
    user_color: str = "#3b82f6"

# API Functions for FastAPI integration
def get_collaboration_routes():
    """Returns routes to be included in main FastAPI app"""
    from fastapi import APIRouter
    
    router = APIRouter(prefix="/api/collaboration", tags=["collaboration"])
    
    @router.post("/rooms")
    async def create_room(request: CreateRoomRequest):
        """Create a new collaboration room"""
        room = CollaborationRoom(
            id=str(uuid.uuid4()),
            document_id=request.document_id,
            document_type=request.document_type,
            title=request.title,
            created_by='anonymous',  # For now, not requiring authentication
            max_participants=request.max_participants
        )
        
        # In production, save to database
        # await rooms_collection.insert_one(room.dict())
        
        return {"room": room.dict(), "success": True}
    
    @router.get("/rooms/{room_id}")
    async def get_room(room_id: str):
        """Get room information and statistics"""
        stats = connection_manager.get_room_stats(room_id)
        return {
            "room_id": room_id,
            "stats": stats,
            "success": True
        }
    
    @router.get("/rooms/{room_id}/messages")
    async def get_room_messages(room_id: str, limit: int = 50):
        """Get recent messages from a room"""
        messages = connection_manager.room_messages.get(room_id, [])
        return {
            "messages": [msg.dict() for msg in messages[-limit:]],
            "total": len(messages),
            "success": True
        }
    
    @router.get("/rooms/{room_id}/changes")
    async def get_room_changes(room_id: str, limit: int = 100):
        """Get recent document changes from a room"""
        changes = connection_manager.room_changes.get(room_id, [])
        return {
            "changes": [change.dict() for change in changes[-limit:]],
            "total": len(changes),
            "success": True
        }
    
    @router.websocket("/rooms/{room_id}/ws")
    async def websocket_endpoint(websocket: WebSocket, room_id: str, user_id: str, user_name: str, user_avatar: str = None, user_color: str = "#3b82f6"):
        """WebSocket endpoint for real-time collaboration"""
        user_data = {
            "id": user_id,
            "name": user_name,
            "avatar": user_avatar,
            "color": user_color
        }
        
        await connection_manager.connect(websocket, room_id, user_id, user_data)
        
        try:
            while True:
                # Receive message from client
                data = await websocket.receive_json()
                await connection_manager.handle_message(room_id, user_id, data)
                
        except WebSocketDisconnect:
            await connection_manager.disconnect(room_id, user_id)
        except Exception as e:
            logger.error(f"WebSocket error for user {user_id} in room {room_id}: {e}")
            await connection_manager.disconnect(room_id, user_id)
    
    return router

# Additional utility functions
class CollaborationSystemManager:
    @staticmethod
    async def cleanup_inactive_rooms(max_idle_hours: int = 24):
        """Clean up rooms that have been inactive for too long"""
        # This would be called periodically to clean up resources
        pass
    
    @staticmethod
    async def save_room_state(room_id: str):
        """Save room state to persistent storage"""
        # Implementation would save messages, changes, etc. to database
        pass
    
    @staticmethod
    async def restore_room_state(room_id: str):
        """Restore room state from persistent storage"""
        # Implementation would load saved room data
        pass

# Export for use in main.py
__all__ = ['get_collaboration_routes', 'connection_manager', 'CollaborationSystemManager']