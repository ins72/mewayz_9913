"""
Professional Admin Logging System
Complete audit logging with admin dashboard visibility and filtering
"""

import logging
import json
import os
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, List
from pathlib import Path
import asyncio
from fastapi import Request
import aiofiles

class AdminLogger:
    """Professional logging system with admin dashboard integration"""
    
    def __init__(self):
        self.logger = logging.getLogger("mewayz_admin")
        self.logger.setLevel(logging.INFO)
        
        # Ensure logs directory exists
        self.logs_dir = Path("logs")
        self.logs_dir.mkdir(exist_ok=True)
        
        # Professional formatter with detailed context
        formatter = logging.Formatter(
            '%(asctime)s | %(levelname)s | %(name)s | %(funcName)s:%(lineno)d | %(message)s'
        )
        
        # File handlers for different log types
        self._setup_file_handlers(formatter)
        
        # Console handler for development
        console_handler = logging.StreamHandler()
        console_handler.setFormatter(formatter)
        self.logger.addHandler(console_handler)
        
    def _setup_file_handlers(self, formatter):
        """Setup file handlers for different log categories"""
        handlers = {
            'admin_system.log': logging.INFO,
            'api_calls.log': logging.INFO,
            'external_apis.log': logging.INFO,
            'security.log': logging.WARNING,
            'errors.log': logging.ERROR
        }
        
        for filename, level in handlers.items():
            handler = logging.FileHandler(self.logs_dir / filename, encoding='utf-8')
            handler.setLevel(level)
            handler.setFormatter(formatter)
            self.logger.addHandler(handler)
    
    def log_api_call(self, request: Request, user_id: str, action: str, details: Dict[str, Any]):
        """Log API call with comprehensive details"""
        log_entry = {
            "type": "API_CALL",
            "timestamp": datetime.utcnow().isoformat(),
            "user_id": user_id,
            "action": action,
            "ip_address": request.client.host if request.client else "unknown",
            "user_agent": request.headers.get("user-agent", "unknown"),
            "endpoint": str(request.url),
            "method": request.method,
            "details": details,
            "session_id": request.headers.get("x-session-id", "unknown"),
            "request_id": request.headers.get("x-request-id", f"req_{int(datetime.utcnow().timestamp())}")
        }
        
        self.logger.info(f"API_CALL: {json.dumps(log_entry, indent=2)}")
        
        # Also store in database for admin dashboard
        asyncio.create_task(self._store_log_in_db(log_entry))
    
    def log_external_api_call(self, service: str, endpoint: str, status: int, 
                             response_time: float, error: str = None, request_data: Dict = None):
        """Log external API calls with detailed metrics"""
        log_entry = {
            "type": "EXTERNAL_API",
            "timestamp": datetime.utcnow().isoformat(),
            "service": service,
            "endpoint": endpoint,
            "status_code": status,
            "response_time_ms": response_time,
            "success": status < 400,
            "error": error,
            "request_data": request_data or {},
            "performance_category": self._categorize_performance(response_time)
        }
        
        level = logging.ERROR if status >= 400 else logging.INFO
        self.logger.log(level, f"EXTERNAL_API: {json.dumps(log_entry, indent=2)}")
        
        asyncio.create_task(self._store_log_in_db(log_entry))
    
    def log_system_event(self, event_type: str, details: Dict[str, Any], severity: str = "INFO"):
        """Log system events with categorization"""
        log_entry = {
            "type": "SYSTEM_EVENT",
            "timestamp": datetime.utcnow().isoformat(),
            "event_type": event_type,
            "severity": severity,
            "details": details,
            "category": self._categorize_event(event_type)
        }
        
        level = getattr(logging, severity.upper(), logging.INFO)
        self.logger.log(level, f"SYSTEM_EVENT: {json.dumps(log_entry, indent=2)}")
        
        asyncio.create_task(self._store_log_in_db(log_entry))
    
    def log_security_event(self, event_type: str, user_id: str, ip_address: str, 
                          details: Dict[str, Any], severity: str = "WARNING"):
        """Log security-related events"""
        log_entry = {
            "type": "SECURITY_EVENT",
            "timestamp": datetime.utcnow().isoformat(),
            "event_type": event_type,
            "user_id": user_id,
            "ip_address": ip_address,
            "severity": severity,
            "details": details,
            "risk_level": self._assess_security_risk(event_type, details)
        }
        
        level = getattr(logging, severity.upper(), logging.WARNING)
        self.logger.log(level, f"SECURITY_EVENT: {json.dumps(log_entry, indent=2)}")
        
        asyncio.create_task(self._store_log_in_db(log_entry))
    
    def log_payment_event(self, processor: str, transaction_id: str, amount: float, 
                         currency: str, status: str, user_id: str, details: Dict = None):
        """Log payment-related events"""
        log_entry = {
            "type": "PAYMENT_EVENT",
            "timestamp": datetime.utcnow().isoformat(),
            "processor": processor,
            "transaction_id": transaction_id,
            "amount": amount,
            "currency": currency,
            "status": status,
            "user_id": user_id,
            "details": details or {},
            "risk_assessment": self._assess_payment_risk(amount, processor)
        }
        
        level = logging.ERROR if status == "failed" else logging.INFO
        self.logger.log(level, f"PAYMENT_EVENT: {json.dumps(log_entry, indent=2)}")
        
        asyncio.create_task(self._store_log_in_db(log_entry))
    
    async def get_logs_for_admin(self, filters: Dict[str, Any]) -> List[Dict[str, Any]]:
        """Retrieve logs with admin filtering capabilities"""
        try:
            from core.database import get_database
            db = get_database()
            
            # Build query from filters
            query = {}
            if filters.get('level'):
                query['severity'] = filters['level']
            if filters.get('start_date'):
                query['timestamp'] = {"$gte": filters['start_date']}
            if filters.get('end_date'):
                query.setdefault('timestamp', {})['$lte'] = filters['end_date']
            if filters.get('search'):
                query['$text'] = {"$search": filters['search']}
            if filters.get('user_id'):
                query['user_id'] = filters['user_id']
            if filters.get('event_type'):
                query['type'] = filters['event_type']
            
            # Execute query with pagination
            limit = min(filters.get('limit', 100), 1000)  # Max 1000 records
            skip = filters.get('offset', 0)
            
            cursor = db.audit_logs.find(query).sort('timestamp', -1).skip(skip).limit(limit)
            logs = await cursor.to_list(length=limit)
            
            return logs
            
        except Exception as e:
            self.logger.error(f"Failed to retrieve logs for admin: {str(e)}")
            return []
    
    async def _store_log_in_db(self, log_entry: Dict[str, Any]):
        """Store log entry in database for admin dashboard"""
        try:
            from core.database import get_database
            db = get_database()
            
            # Add index fields for efficient querying
            log_entry['_search_text'] = self._create_search_text(log_entry)
            
            await db.audit_logs.insert_one(log_entry)
            
        except Exception as e:
            # Don't log this error to avoid recursion
            print(f"Failed to store log in database: {str(e)}")
    
    def _categorize_performance(self, response_time: float) -> str:
        """Categorize API response performance"""
        if response_time < 100:
            return "excellent"
        elif response_time < 500:
            return "good"
        elif response_time < 1000:
            return "fair"
        else:
            return "poor"
    
    def _categorize_event(self, event_type: str) -> str:
        """Categorize system events"""
        categories = {
            "startup": "system",
            "shutdown": "system",
            "error": "error",
            "authentication": "security",
            "payment": "financial",
            "api": "integration"
        }
        
        for key, category in categories.items():
            if key.lower() in event_type.lower():
                return category
        
        return "general"
    
    def _assess_security_risk(self, event_type: str, details: Dict[str, Any]) -> str:
        """Assess security risk level"""
        high_risk_events = ["failed_login", "unauthorized_access", "injection_attempt"]
        medium_risk_events = ["rate_limit_exceeded", "suspicious_activity"]
        
        if any(risk in event_type.lower() for risk in high_risk_events):
            return "high"
        elif any(risk in event_type.lower() for risk in medium_risk_events):
            return "medium"
        else:
            return "low"
    
    def _assess_payment_risk(self, amount: float, processor: str) -> str:
        """Assess payment transaction risk"""
        if amount > 10000:
            return "high"
        elif amount > 1000:
            return "medium"
        else:
            return "low"
    
    def _create_search_text(self, log_entry: Dict[str, Any]) -> str:
        """Create searchable text from log entry"""
        searchable_fields = [
            str(log_entry.get('type', '')),
            str(log_entry.get('event_type', '')),
            str(log_entry.get('user_id', '')),
            str(log_entry.get('action', '')),
            str(log_entry.get('service', '')),
            json.dumps(log_entry.get('details', {}))
        ]
        
        return ' '.join(searchable_fields).lower()

def setup_logging():
    """Setup comprehensive logging configuration"""
    # Configure root logger
    logging.basicConfig(
        level=logging.INFO,
        format='%(asctime)s - %(name)s - %(levelname)s - %(message)s',
        handlers=[
            logging.StreamHandler(),
            logging.FileHandler('logs/application.log')
        ]
    )
    
    # Configure specific loggers
    loggers_config = {
        'uvicorn': logging.INFO,
        'fastapi': logging.INFO,
        'sqlalchemy': logging.WARNING,
        'httpx': logging.WARNING
    }
    
    for logger_name, level in loggers_config.items():
        logger = logging.getLogger(logger_name)
        logger.setLevel(level)
    
    print("✅ Logging system configured successfully")

# Global admin logger instance
admin_logger = AdminLogger()