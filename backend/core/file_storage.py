"""
Backblaze B2 Cloud Storage Manager
Complete file storage solution with Backblaze B2 integration
"""

import os
import asyncio
import hashlib
from typing import Dict, Any, Optional, List, BinaryIO
from datetime import datetime, timedelta
import mimetypes
from pathlib import Path
import httpx

from core.logging import admin_logger
from core.database import get_database

try:
    from b2sdk.v2 import B2Api, InMemoryAccountInfo, Bucket
    B2_AVAILABLE = True
except ImportError:
    B2_AVAILABLE = False
    B2Api = None
    InMemoryAccountInfo = None
    Bucket = None

class BackblazeStorageManager:
    """Backblaze B2 Cloud Storage integration"""
    
    def __init__(self):
        self.b2_api = None
        self.bucket = None
        self.account_info = None
        self.bucket_id = None
        self.bucket_name = None
        self.fallback_storage = "./uploads"  # Local fallback
        
    async def initialize(self):
        """Initialize Backblaze B2 connection"""
        try:
            # Load configuration from database or environment
            config = await self._load_storage_configuration()
            
            if B2_AVAILABLE and config.get("key_id") and config.get("app_key"):
                await self._initialize_b2_api(config)
            else:
                admin_logger.log_system_event("BACKBLAZE_UNAVAILABLE", {
                    "reason": "SDK not installed or credentials missing",
                    "fallback": "local storage"
                }, "WARNING")
                await self._initialize_local_storage()
                
        except Exception as e:
            admin_logger.log_system_event("STORAGE_INITIALIZATION_FAILED", {
                "error": str(e)
            }, "ERROR")
            await self._initialize_local_storage()
    
    async def _load_storage_configuration(self) -> Dict[str, Any]:
        """Load storage configuration from database"""
        try:
            db = get_database()
            config = await db.api_configuration.find_one({"type": "storage"})
            
            if config:
                from core.security import security_manager
                return {
                    "key_id": security_manager.decrypt_sensitive_data(config.get("backblaze_key_id", "")),
                    "app_key": security_manager.decrypt_sensitive_data(config.get("backblaze_app_key", "")),
                    "bucket_id": config.get("backblaze_bucket_id", ""),
                    "bucket_name": config.get("backblaze_bucket_name", "")
                }
            else:
                # Fallback to environment variables
                return {
                    "key_id": os.getenv("BACKBLAZE_KEY_ID"),
                    "app_key": os.getenv("BACKBLAZE_APP_KEY"),
                    "bucket_id": os.getenv("BACKBLAZE_BUCKET_ID"),
                    "bucket_name": os.getenv("BACKBLAZE_BUCKET_NAME", "mewayz-storage")
                }
                
        except Exception as e:
            admin_logger.log_system_event("STORAGE_CONFIG_LOAD_FAILED", {
                "error": str(e)
            }, "ERROR")
            return {}
    
    async def _initialize_b2_api(self, config: Dict[str, Any]):
        """Initialize Backblaze B2 API"""
        try:
            self.account_info = InMemoryAccountInfo()
            self.b2_api = B2Api(self.account_info)
            
            # Run authorization in thread pool
            loop = asyncio.get_event_loop()
            await loop.run_in_executor(
                None,
                self.b2_api.authorize_account,
                "production",
                config["key_id"],
                config["app_key"]
            )
            
            # Get or create bucket
            self.bucket_id = config.get("bucket_id")
            self.bucket_name = config.get("bucket_name", "mewayz-storage")
            
            if self.bucket_id:
                self.bucket = await loop.run_in_executor(
                    None,
                    self.b2_api.get_bucket_by_id,
                    self.bucket_id
                )
            else:
                # Try to find bucket by name
                buckets = await loop.run_in_executor(
                    None,
                    self.b2_api.list_buckets
                )
                
                for bucket in buckets:
                    if bucket.name == self.bucket_name:
                        self.bucket = bucket
                        self.bucket_id = bucket.id_
                        break
                
                if not self.bucket:
                    # Create new bucket
                    self.bucket = await loop.run_in_executor(
                        None,
                        self.b2_api.create_bucket,
                        self.bucket_name,
                        "allPrivate"
                    )
                    self.bucket_id = self.bucket.id_
            
            admin_logger.log_system_event("BACKBLAZE_INITIALIZED", {
                "bucket_name": self.bucket_name,
                "bucket_id": self.bucket_id
            })
            
        except Exception as e:
            admin_logger.log_system_event("BACKBLAZE_INIT_FAILED", {
                "error": str(e)
            }, "ERROR")
            raise
    
    async def _initialize_local_storage(self):
        """Initialize local storage fallback"""
        try:
            Path(self.fallback_storage).mkdir(parents=True, exist_ok=True)
            admin_logger.log_system_event("LOCAL_STORAGE_INITIALIZED", {
                "path": self.fallback_storage
            })
        except Exception as e:
            admin_logger.log_system_event("LOCAL_STORAGE_INIT_FAILED", {
                "error": str(e)
            }, "ERROR")
            raise
    
    async def upload_file(self, file_data: bytes, filename: str, content_type: str = None,
                         metadata: Dict[str, Any] = None) -> Dict[str, Any]:
        """Upload file to storage"""
        try:
            # Generate unique filename
            file_hash = hashlib.sha256(file_data).hexdigest()[:16]
            unique_filename = f"{datetime.utcnow().strftime('%Y/%m/%d')}/{file_hash}_{filename}"
            
            # Detect content type if not provided
            if not content_type:
                content_type, _ = mimetypes.guess_type(filename)
                if not content_type:
                    content_type = "application/octet-stream"
            
            if self.bucket:
                # Upload to Backblaze B2
                result = await self._upload_to_b2(file_data, unique_filename, content_type, metadata)
            else:
                # Upload to local storage
                result = await self._upload_to_local(file_data, unique_filename, content_type, metadata)
            
            # Store file metadata in database
            await self._store_file_metadata(result, metadata)
            
            admin_logger.log_system_event("FILE_UPLOADED", {
                "filename": filename,
                "unique_filename": unique_filename,
                "size": len(file_data),
                "storage": "b2" if self.bucket else "local"
            })
            
            return result
            
        except Exception as e:
            admin_logger.log_system_event("FILE_UPLOAD_FAILED", {
                "filename": filename,
                "error": str(e)
            }, "ERROR")
            return {
                "success": False,
                "error": str(e)
            }
    
    async def _upload_to_b2(self, file_data: bytes, filename: str, content_type: str,
                           metadata: Dict[str, Any] = None) -> Dict[str, Any]:
        """Upload file to Backblaze B2"""
        try:
            # Prepare metadata
            file_info = {
                "uploaded_at": datetime.utcnow().isoformat(),
                "content_type": content_type,
                "platform": "mewayz"
            }
            
            if metadata:
                file_info.update({f"custom_{k}": str(v) for k, v in metadata.items()})
            
            # Upload file in thread pool
            loop = asyncio.get_event_loop()
            file_version = await loop.run_in_executor(
                None,
                lambda: self.bucket.upload_bytes(
                    file_data,
                    filename,
                    content_type=content_type,
                    file_info=file_info
                )
            )
            
            # Generate download URL
            download_url = await loop.run_in_executor(
                None,
                self.b2_api.get_download_url_for_fileid,
                file_version.id_
            )
            
            return {
                "success": True,
                "file_id": file_version.id_,
                "filename": filename,
                "original_filename": metadata.get("original_filename", filename) if metadata else filename,
                "size": len(file_data),
                "content_type": content_type,
                "storage_backend": "backblaze_b2",
                "bucket_name": self.bucket_name,
                "download_url": download_url,
                "uploaded_at": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            admin_logger.log_system_event("B2_UPLOAD_FAILED", {
                "filename": filename,
                "error": str(e)
            }, "ERROR")
            raise
    
    async def _upload_to_local(self, file_data: bytes, filename: str, content_type: str,
                              metadata: Dict[str, Any] = None) -> Dict[str, Any]:
        """Upload file to local storage"""
        try:
            file_path = Path(self.fallback_storage) / filename
            file_path.parent.mkdir(parents=True, exist_ok=True)
            
            # Write file asynchronously
            import aiofiles
            async with aiofiles.open(file_path, 'wb') as f:
                await f.write(file_data)
            
            # Generate local URL
            local_url = f"/files/{filename}"
            
            return {
                "success": True,
                "file_id": str(file_path),
                "filename": filename,
                "original_filename": metadata.get("original_filename", filename) if metadata else filename,
                "size": len(file_data),
                "content_type": content_type,
                "storage_backend": "local",
                "file_path": str(file_path),
                "download_url": local_url,
                "uploaded_at": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            admin_logger.log_system_event("LOCAL_UPLOAD_FAILED", {
                "filename": filename,
                "error": str(e)
            }, "ERROR")
            raise
    
    async def download_file(self, file_id: str) -> Optional[Dict[str, Any]]:
        """Download file by file ID"""
        try:
            if self.bucket:
                return await self._download_from_b2(file_id)
            else:
                return await self._download_from_local(file_id)
                
        except Exception as e:
            admin_logger.log_system_event("FILE_DOWNLOAD_FAILED", {
                "file_id": file_id,
                "error": str(e)
            }, "ERROR")
            return None
    
    async def _download_from_b2(self, file_id: str) -> Optional[Dict[str, Any]]:
        """Download file from Backblaze B2"""
        try:
            loop = asyncio.get_event_loop()
            
            # Get file info
            file_version = await loop.run_in_executor(
                None,
                self.b2_api.get_file_info,
                file_id
            )
            
            # Download file content
            response = await loop.run_in_executor(
                None,
                self.b2_api.download_file_by_id,
                file_id
            )
            
            return {
                "success": True,
                "file_id": file_id,
                "filename": file_version.file_name,
                "content": response.content,
                "content_type": file_version.content_type,
                "size": file_version.size
            }
            
        except Exception as e:
            admin_logger.log_system_event("B2_DOWNLOAD_FAILED", {
                "file_id": file_id,
                "error": str(e)
            }, "ERROR")
            return None
    
    async def _download_from_local(self, file_path: str) -> Optional[Dict[str, Any]]:
        """Download file from local storage"""
        try:
            path = Path(file_path)
            if not path.exists():
                return None
            
            import aiofiles
            async with aiofiles.open(path, 'rb') as f:
                content = await f.read()
            
            content_type, _ = mimetypes.guess_type(str(path))
            
            return {
                "success": True,
                "file_id": str(path),
                "filename": path.name,
                "content": content,
                "content_type": content_type or "application/octet-stream",
                "size": len(content)
            }
            
        except Exception as e:
            admin_logger.log_system_event("LOCAL_DOWNLOAD_FAILED", {
                "file_path": file_path,
                "error": str(e)
            }, "ERROR")
            return None
    
    async def delete_file(self, file_id: str) -> bool:
        """Delete file from storage"""
        try:
            if self.bucket:
                result = await self._delete_from_b2(file_id)
            else:
                result = await self._delete_from_local(file_id)
            
            if result:
                # Remove from database
                await self._remove_file_metadata(file_id)
                
                admin_logger.log_system_event("FILE_DELETED", {
                    "file_id": file_id,
                    "storage": "b2" if self.bucket else "local"
                })
            
            return result
            
        except Exception as e:
            admin_logger.log_system_event("FILE_DELETE_FAILED", {
                "file_id": file_id,
                "error": str(e)
            }, "ERROR")
            return False
    
    async def _delete_from_b2(self, file_id: str) -> bool:
        """Delete file from Backblaze B2"""
        try:
            loop = asyncio.get_event_loop()
            await loop.run_in_executor(
                None,
                self.b2_api.delete_file_version,
                file_id,
                file_id  # B2 requires both file_id and file_name
            )
            return True
            
        except Exception as e:
            admin_logger.log_system_event("B2_DELETE_FAILED", {
                "file_id": file_id,
                "error": str(e)
            }, "ERROR")
            return False
    
    async def _delete_from_local(self, file_path: str) -> bool:
        """Delete file from local storage"""
        try:
            path = Path(file_path)
            if path.exists():
                path.unlink()
            return True
            
        except Exception as e:
            admin_logger.log_system_event("LOCAL_DELETE_FAILED", {
                "file_path": file_path,
                "error": str(e)
            }, "ERROR")
            return False
    
    async def list_files(self, prefix: str = None, limit: int = 100) -> List[Dict[str, Any]]:
        """List files in storage"""
        try:
            if self.bucket:
                return await self._list_b2_files(prefix, limit)
            else:
                return await self._list_local_files(prefix, limit)
                
        except Exception as e:
            admin_logger.log_system_event("FILE_LIST_FAILED", {
                "prefix": prefix,
                "error": str(e)
            }, "ERROR")
            return []
    
    async def _list_b2_files(self, prefix: str = None, limit: int = 100) -> List[Dict[str, Any]]:
        """List files in Backblaze B2"""
        try:
            loop = asyncio.get_event_loop()
            
            file_versions = await loop.run_in_executor(
                None,
                lambda: list(self.bucket.ls(folder_to_list=prefix, recursive=True, fetch_count=limit))
            )
            
            files = []
            for file_version_info, folder_name in file_versions[:limit]:
                if folder_name is None:  # It's a file, not a folder
                    files.append({
                        "file_id": file_version_info.id_,
                        "filename": file_version_info.file_name,
                        "size": file_version_info.size,
                        "content_type": getattr(file_version_info, 'content_type', 'unknown'),
                        "uploaded_at": getattr(file_version_info, 'upload_timestamp', None),
                        "storage_backend": "backblaze_b2"
                    })
            
            return files
            
        except Exception as e:
            admin_logger.log_system_event("B2_LIST_FAILED", {
                "prefix": prefix,
                "error": str(e)
            }, "ERROR")
            return []
    
    async def _list_local_files(self, prefix: str = None, limit: int = 100) -> List[Dict[str, Any]]:
        """List files in local storage"""
        try:
            storage_path = Path(self.fallback_storage)
            pattern = f"{prefix}*" if prefix else "*"
            
            files = []
            for file_path in storage_path.rglob(pattern):
                if file_path.is_file() and len(files) < limit:
                    stat = file_path.stat()
                    content_type, _ = mimetypes.guess_type(str(file_path))
                    
                    files.append({
                        "file_id": str(file_path),
                        "filename": file_path.name,
                        "size": stat.st_size,
                        "content_type": content_type or "application/octet-stream",
                        "uploaded_at": datetime.fromtimestamp(stat.st_mtime).isoformat(),
                        "storage_backend": "local"
                    })
            
            return files
            
        except Exception as e:
            admin_logger.log_system_event("LOCAL_LIST_FAILED", {
                "prefix": prefix,
                "error": str(e)
            }, "ERROR")
            return []
    
    async def get_storage_stats(self) -> Dict[str, Any]:
        """Get storage statistics"""
        try:
            if self.bucket:
                return await self._get_b2_stats()
            else:
                return await self._get_local_stats()
                
        except Exception as e:
            admin_logger.log_system_event("STORAGE_STATS_FAILED", {
                "error": str(e)
            }, "ERROR")
            return {}
    
    async def _get_b2_stats(self) -> Dict[str, Any]:
        """Get Backblaze B2 storage statistics"""
        try:
            loop = asyncio.get_event_loop()
            
            # This is a simplified version - B2 doesn't provide direct bucket stats
            files = await self._list_b2_files(limit=1000)
            
            total_size = sum(file["size"] for file in files)
            total_files = len(files)
            
            return {
                "storage_backend": "backblaze_b2",
                "bucket_name": self.bucket_name,
                "bucket_id": self.bucket_id,
                "total_files": total_files,
                "total_size_bytes": total_size,
                "total_size_mb": round(total_size / 1024 / 1024, 2)
            }
            
        except Exception as e:
            return {
                "storage_backend": "backblaze_b2",
                "error": str(e)
            }
    
    async def _get_local_stats(self) -> Dict[str, Any]:
        """Get local storage statistics"""
        try:
            storage_path = Path(self.fallback_storage)
            
            total_size = 0
            total_files = 0
            
            for file_path in storage_path.rglob("*"):
                if file_path.is_file():
                    total_files += 1
                    total_size += file_path.stat().st_size
            
            return {
                "storage_backend": "local",
                "storage_path": str(storage_path),
                "total_files": total_files,
                "total_size_bytes": total_size,
                "total_size_mb": round(total_size / 1024 / 1024, 2)
            }
            
        except Exception as e:
            return {
                "storage_backend": "local",
                "error": str(e)
            }
    
    async def _store_file_metadata(self, file_result: Dict[str, Any], metadata: Dict[str, Any] = None):
        """Store file metadata in database"""
        try:
            db = get_database()
            
            file_record = {
                "file_id": file_result["file_id"],
                "filename": file_result["filename"],
                "original_filename": file_result.get("original_filename", file_result["filename"]),
                "size": file_result["size"],
                "content_type": file_result["content_type"],
                "storage_backend": file_result["storage_backend"],
                "download_url": file_result.get("download_url"),
                "uploaded_at": datetime.utcnow(),
                "metadata": metadata or {}
            }
            
            await db.file_storage.insert_one(file_record)
            
        except Exception as e:
            admin_logger.log_system_event("FILE_METADATA_STORE_FAILED", {
                "file_id": file_result.get("file_id"),
                "error": str(e)
            }, "WARNING")
    
    async def _remove_file_metadata(self, file_id: str):
        """Remove file metadata from database"""
        try:
            db = get_database()
            await db.file_storage.delete_one({"file_id": file_id})
            
        except Exception as e:
            admin_logger.log_system_event("FILE_METADATA_DELETE_FAILED", {
                "file_id": file_id,
                "error": str(e)
            }, "WARNING")
    
    async def get_status(self) -> Dict[str, Any]:
        """Get storage service status"""
        try:
            stats = await self.get_storage_stats()
            
            return {
                "status": "operational",
                "backend": "backblaze_b2" if self.bucket else "local",
                "bucket_name": self.bucket_name if self.bucket else None,
                "stats": stats,
                "last_checked": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "status": "error",
                "error": str(e),
                "last_checked": datetime.utcnow().isoformat()
            }

# Global storage manager instance
storage_manager = BackblazeStorageManager()