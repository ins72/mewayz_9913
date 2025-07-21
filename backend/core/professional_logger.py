"""
Professional Admin Logging System
Comprehensive logging system with admin visibility, filtering, and real-time monitoring
"""
import logging
import json
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, List
import traceback
from enum import Enum
from core.database import get_database

class LogLevel(str, Enum):
    DEBUG = "DEBUG"
    INFO = "INFO"
    WARNING = "WARNING"
    ERROR = "ERROR"
    CRITICAL = "CRITICAL"

class LogCategory(str, Enum):
    SYSTEM = "SYSTEM"
    API = "API"
    DATABASE = "DATABASE"
    EXTERNAL_API = "EXTERNAL_API"
    AUTHENTICATION = "AUTHENTICATION"
    PAYMENT = "PAYMENT"
    EMAIL = "EMAIL"
    FILE_STORAGE = "FILE_STORAGE"
    AI_SERVICE = "AI_SERVICE"
    ADMIN = "ADMIN"
    SECURITY = "SECURITY"

class ProfessionalLogger:
    """Professional logging system with admin dashboard integration"""
    
    def __init__(self):
        self.db = None
        self.setup_file_logger()
    
    async def get_database(self):
        """Get database connection with lazy initialization"""
        if not self.db:
            self.db = get_database()
        return self.db
    
    def setup_file_logger(self):
        """Setup file-based logging for backup"""
        import os
        os.makedirs('/app/logs', exist_ok=True)
        
        self.file_logger = logging.getLogger("mewayz_admin")
        self.file_logger.setLevel(logging.INFO)
        
        if not self.file_logger.handlers:
            # Professional formatter with detailed context
            formatter = logging.Formatter(
                '%(asctime)s | %(levelname)s | %(name)s | %(funcName)s:%(lineno)d | %(message)s'
            )
            
            # File handler for persistent logs
            file_handler = logging.FileHandler('/app/logs/admin_system.log')
            file_handler.setFormatter(formatter)
            self.file_logger.addHandler(file_handler)
            
            # Error file handler
            error_handler = logging.FileHandler('/app/logs/admin_errors.log')
            error_handler.setLevel(logging.ERROR)
            error_handler.setFormatter(formatter)
            self.file_logger.addHandler(error_handler)
    
    async def log(
        self, 
        level: LogLevel, 
        category: LogCategory, 
        message: str, 
        details: Dict[str, Any] = None,
        user_id: str = None,
        request_id: str = None,
        ip_address: str = None,
        user_agent: str = None,
        endpoint: str = None,
        method: str = None,
        response_time: float = None,
        status_code: int = None,
        error: Exception = None
    ):
        """Comprehensive logging with admin visibility"""
        try:
            # Create comprehensive log entry
            log_entry = {
                "timestamp": datetime.utcnow(),
                "level": level.value,
                "category": category.value,
                "message": message,
                "details": details or {},
                "user_id": user_id,
                "request_id": request_id,
                "ip_address": ip_address,
                "user_agent": user_agent,
                "endpoint": endpoint,
                "method": method,
                "response_time": response_time,
                "status_code": status_code,
                "error_details": None,
                "stack_trace": None
            }
            
            # Add error details if present
            if error:
                log_entry["error_details"] = {
                    "type": type(error).__name__,
                    "message": str(error),
                }
                log_entry["stack_trace"] = traceback.format_exc()
            
            # Store in database for admin dashboard
            db = await self.get_database()
            await db.admin_system_logs.insert_one(log_entry)
            
            # Also log to file for backup
            log_message = f"{category.value} | {message}"
            if details:
                log_message += f" | Details: {json.dumps(details, default=str)}"
            
            if level == LogLevel.DEBUG:
                self.file_logger.debug(log_message)
            elif level == LogLevel.INFO:
                self.file_logger.info(log_message)
            elif level == LogLevel.WARNING:
                self.file_logger.warning(log_message)
            elif level == LogLevel.ERROR:
                self.file_logger.error(log_message)
            elif level == LogLevel.CRITICAL:
                self.file_logger.critical(log_message)
            
        except Exception as e:
            # Fallback to file logging if database fails
            self.file_logger.error(f"Failed to log to database: {str(e)} | Original message: {message}")
    
    async def log_api_request(
        self,
        endpoint: str,
        method: str,
        user_id: str = None,
        ip_address: str = None,
        user_agent: str = None,
        request_data: Dict[str, Any] = None,
        response_data: Dict[str, Any] = None,
        response_time: float = None,
        status_code: int = None,
        error: Exception = None
    ):
        """Log API requests with comprehensive details"""
        level = LogLevel.ERROR if error or (status_code and status_code >= 400) else LogLevel.INFO
        
        details = {
            "request_data": request_data,
            "response_data": response_data if status_code != 200 else None,  # Don't log successful response data for privacy
            "request_size": len(str(request_data)) if request_data else 0,
            "response_size": len(str(response_data)) if response_data else 0
        }
        
        message = f"API Request: {method} {endpoint}"
        if error:
            message += f" - Error: {str(error)}"
        
        await self.log(
            level=level,
            category=LogCategory.API,
            message=message,
            details=details,
            user_id=user_id,
            ip_address=ip_address,
            user_agent=user_agent,
            endpoint=endpoint,
            method=method,
            response_time=response_time,
            status_code=status_code,
            error=error
        )
    
    async def log_external_api_call(
        self,
        service: str,
        endpoint: str,
        method: str,
        status_code: int,
        response_time: float,
        request_data: Dict[str, Any] = None,
        error: Exception = None
    ):
        """Log external API calls"""
        level = LogLevel.ERROR if error or status_code >= 400 else LogLevel.INFO
        
        details = {
            "service": service,
            "request_data": request_data,
            "external_endpoint": endpoint
        }
        
        message = f"External API: {service} {method} {endpoint} - Status: {status_code}"
        if error:
            message += f" - Error: {str(error)}"
        
        await self.log(
            level=level,
            category=LogCategory.EXTERNAL_API,
            message=message,
            details=details,
            response_time=response_time,
            status_code=status_code,
            error=error
        )
    
    async def log_database_operation(
        self,
        operation: str,
        collection: str,
        query: Dict[str, Any] = None,
        result_count: int = None,
        execution_time: float = None,
        error: Exception = None
    ):
        """Log database operations"""
        level = LogLevel.ERROR if error else LogLevel.DEBUG
        
        details = {
            "operation": operation,
            "collection": collection,
            "query": query,
            "result_count": result_count,
            "execution_time": execution_time
        }
        
        message = f"Database {operation}: {collection}"
        if result_count is not None:
            message += f" - {result_count} documents"
        if error:
            message += f" - Error: {str(error)}"
        
        await self.log(
            level=level,
            category=LogCategory.DATABASE,
            message=message,
            details=details,
            error=error
        )
    
    async def log_security_event(
        self,
        event_type: str,
        user_id: str = None,
        ip_address: str = None,
        details: Dict[str, Any] = None,
        severity: LogLevel = LogLevel.WARNING
    ):
        """Log security-related events"""
        message = f"Security Event: {event_type}"
        
        security_details = {
            "event_type": event_type,
            "severity": severity.value,
            **(details or {})
        }
        
        await self.log(
            level=severity,
            category=LogCategory.SECURITY,
            message=message,
            details=security_details,
            user_id=user_id,
            ip_address=ip_address
        )
    
    async def get_admin_logs(
        self,
        level: Optional[LogLevel] = None,
        category: Optional[LogCategory] = None,
        start_date: Optional[datetime] = None,
        end_date: Optional[datetime] = None,
        search: Optional[str] = None,
        user_id: Optional[str] = None,
        limit: int = 100,
        offset: int = 0
    ) -> Dict[str, Any]:
        """Get logs for admin dashboard with filtering"""
        try:
            db = await self.get_database()
            
            # Build query
            query = {}
            
            if level:
                query["level"] = level.value
            
            if category:
                query["category"] = category.value
            
            if start_date or end_date:
                query["timestamp"] = {}
                if start_date:
                    query["timestamp"]["$gte"] = start_date
                if end_date:
                    query["timestamp"]["$lte"] = end_date
            
            if search:
                query["$or"] = [
                    {"message": {"$regex": search, "$options": "i"}},
                    {"details": {"$regex": search, "$options": "i"}}
                ]
            
            if user_id:
                query["user_id"] = user_id
            
            # Get total count
            total_count = await db.admin_system_logs.count_documents(query)
            
            # Get logs
            cursor = db.admin_system_logs.find(query)\
                .sort("timestamp", -1)\
                .skip(offset)\
                .limit(limit)
            
            logs = await cursor.to_list(length=limit)
            
            # Convert ObjectId to string for JSON serialization
            for log in logs:
                if "_id" in log:
                    log["_id"] = str(log["_id"])
                if "timestamp" in log:
                    log["timestamp"] = log["timestamp"].isoformat()
            
            return {
                "success": True,
                "logs": logs,
                "total": total_count,
                "count": len(logs),
                "filters_applied": {
                    "level": level.value if level else None,
                    "category": category.value if category else None,
                    "start_date": start_date.isoformat() if start_date else None,
                    "end_date": end_date.isoformat() if end_date else None,
                    "search": search,
                    "user_id": user_id
                }
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": f"Failed to retrieve logs: {str(e)}",
                "logs": [],
                "total": 0
            }
    
    async def get_log_statistics(self) -> Dict[str, Any]:
        """Get log statistics for admin dashboard"""
        try:
            db = await self.get_database()
            
            # Get statistics for the last 24 hours
            since = datetime.utcnow() - timedelta(days=1)
            
            # Aggregate by level
            level_stats = await db.admin_system_logs.aggregate([
                {"$match": {"timestamp": {"$gte": since}}},
                {"$group": {"_id": "$level", "count": {"$sum": 1}}},
                {"$sort": {"count": -1}}
            ]).to_list(length=None)
            
            # Aggregate by category
            category_stats = await db.admin_system_logs.aggregate([
                {"$match": {"timestamp": {"$gte": since}}},
                {"$group": {"_id": "$category", "count": {"$sum": 1}}},
                {"$sort": {"count": -1}}
            ]).to_list(length=None)
            
            # Get error rate
            total_logs = await db.admin_system_logs.count_documents({"timestamp": {"$gte": since}})
            error_logs = await db.admin_system_logs.count_documents({
                "timestamp": {"$gte": since},
                "level": {"$in": ["ERROR", "CRITICAL"]}
            })
            
            return {
                "success": True,
                "statistics": {
                    "total_logs_24h": total_logs,
                    "error_rate": round((error_logs / total_logs * 100) if total_logs > 0 else 0, 2),
                    "by_level": {stat["_id"]: stat["count"] for stat in level_stats},
                    "by_category": {stat["_id"]: stat["count"] for stat in category_stats},
                    "time_period": "last_24_hours"
                }
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": f"Failed to get log statistics: {str(e)}"
            }

# Global instance
professional_logger = ProfessionalLogger()