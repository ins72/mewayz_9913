"""
Media Library Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
import uuid
import base64
import hashlib
from core.database import get_database

class MediaLibraryService:
    """Service for media library operations"""
    
    @staticmethod
    async def get_user_media(user_id: str, media_type: str = "all", folder_id: str = None):
        """Get user's media files"""
        db = await get_database()
        
        query = {"user_id": user_id}
        
        if media_type != "all":
            query["type"] = media_type
        
        if folder_id:
            query["folder_id"] = folder_id
        else:
            query["folder_id"] = {"$exists": False}  # Root folder
        
        media_files = await db.media_files.find(query).sort("created_at", -1).to_list(length=None)
        return media_files
    
    @staticmethod
    async def upload_media(user_id: str, file_data: Dict[str, Any]):
        """Upload media file"""
        db = await get_database()
        
        # In a real implementation, this would handle actual file upload
        # For now, we'll store the base64 data and metadata
        
        file_content = file_data.get("content", "")
        if file_content.startswith("data:"):
            # Remove data URL prefix if present
            file_content = file_content.split(",", 1)[1] if "," in file_content else file_content
        
        # Calculate file hash for deduplication
        file_hash = hashlib.md5(file_content.encode()).hexdigest()
        
        # Check for existing file
        existing_file = await db.media_files.find_one({
            "user_id": user_id,
            "file_hash": file_hash
        })
        
        if existing_file:
            return {"success": False, "error": "File already exists", "existing_file": existing_file}
        
        media_file = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "filename": file_data.get("filename"),
            "original_name": file_data.get("original_name", file_data.get("filename")),
            "type": file_data.get("type", "image"),
            "mime_type": file_data.get("mime_type", "application/octet-stream"),
            "size": len(file_content) if file_content else 0,
            "file_hash": file_hash,
            "content": file_content,  # In production, this would be a file path/URL
            "folder_id": file_data.get("folder_id"),
            "alt_text": file_data.get("alt_text", ""),
            "description": file_data.get("description", ""),
            "tags": file_data.get("tags", []),
            "metadata": {
                "width": file_data.get("width"),
                "height": file_data.get("height"),
                "duration": file_data.get("duration"),  # for videos
                "format": file_data.get("format")
            },
            "usage_count": 0,
            "is_public": file_data.get("is_public", False),
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        
        result = await db.media_files.insert_one(media_file)
        return {"success": True, "file": media_file}
    
    @staticmethod
    async def create_folder(user_id: str, folder_data: Dict[str, Any]):
        """Create media folder"""
        db = await get_database()
        
        folder = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "name": folder_data.get("name"),
            "description": folder_data.get("description", ""),
            "parent_folder_id": folder_data.get("parent_folder_id"),
            "color": folder_data.get("color", "#007bff"),
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        
        result = await db.media_folders.insert_one(folder)
        return folder
    
    @staticmethod
    async def get_user_folders(user_id: str):
        """Get user's media folders"""
        db = await get_database()
        
        folders = await db.media_folders.find({"user_id": user_id}).sort("name", 1).to_list(length=None)
        return folders
    
    @staticmethod
    async def get_media_by_id(media_id: str, user_id: str):
        """Get specific media file"""
        db = await get_database()
        
        media_file = await db.media_files.find_one({
            "_id": media_id,
            "user_id": user_id
        })
        return media_file
    
    @staticmethod
    async def update_media(media_id: str, user_id: str, update_data: Dict[str, Any]):
        """Update media file metadata"""
        db = await get_database()
        
        update_fields = {
            "alt_text": update_data.get("alt_text"),
            "description": update_data.get("description"),
            "tags": update_data.get("tags"),
            "folder_id": update_data.get("folder_id"),
            "updated_at": datetime.utcnow()
        }
        
        # Remove None values
        update_fields = {k: v for k, v in update_fields.items() if v is not None}
        
        result = await db.media_files.update_one(
            {"_id": media_id, "user_id": user_id},
            {"$set": update_fields}
        )
        
        return result.modified_count > 0
    
    @staticmethod
    async def delete_media(media_id: str, user_id: str):
        """Delete media file"""
        db = await get_database()
        
        result = await db.media_files.delete_one({
            "_id": media_id,
            "user_id": user_id
        })
        
        return result.deleted_count > 0
    
    @staticmethod
    async def get_media_stats(user_id: str):
        """Get user's media library statistics"""
        db = await get_database()
        
        # Count files by type
        pipeline = [
            {"$match": {"user_id": user_id}},
            {"$group": {
                "_id": "$type",
                "count": {"$sum": 1},
                "total_size": {"$sum": "$size"}
            }}
        ]
        
        type_stats = await db.media_files.aggregate(pipeline).to_list(length=None)
        
        # Total statistics
        total_files = await db.media_files.count_documents({"user_id": user_id})
        total_size = sum(stat["total_size"] for stat in type_stats)
        
        stats = {
            "total_files": total_files,
            "total_size": total_size,
            "total_size_mb": round(total_size / (1024 * 1024), 2),
            "by_type": {stat["_id"]: {"count": stat["count"], "size": stat["total_size"]} for stat in type_stats},
            "storage_limit": 1024 * 1024 * 1024,  # 1GB default limit
            "usage_percentage": round((total_size / (1024 * 1024 * 1024)) * 100, 1)
        }
        
        return stats

# Global service instance
media_library_service = MediaLibraryService()
