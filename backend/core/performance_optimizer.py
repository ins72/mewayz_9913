"""
Performance Optimizer
Redis caching, connection pooling, and performance monitoring
"""
import redis
import asyncio
import time
import json
import gzip
from typing import Dict, Any, Optional, Union, List
from datetime import datetime, timedelta
from functools import wraps
import pickle
import hashlib

from core.database import get_database
from core.professional_logger import professional_logger, LogLevel, LogCategory

class RedisCache:
    """Redis caching system with connection pooling"""
    
    def __init__(self):
        self.pool = None
        self.redis_client = None
        self._connection_attempts = 0
        self._max_retries = 3
    
    async def initialize(self, redis_url: str = "redis://localhost:6379/0"):
        """Initialize Redis connection with pool"""
        try:
            self.pool = redis.ConnectionPool.from_url(
                redis_url,
                max_connections=20,
                retry_on_timeout=True,
                socket_connect_timeout=5,
                socket_timeout=5
            )
            
            self.redis_client = redis.Redis(connection_pool=self.pool, decode_responses=True)
            
            # Test connection
            await self._test_connection()
            
            await professional_logger.log(
                LogLevel.INFO, LogCategory.SYSTEM,
                "Redis cache initialized successfully",
                details={"pool_size": 20}
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.WARNING, LogCategory.SYSTEM,
                f"Failed to initialize Redis cache: {str(e)}",
                error=e
            )
            self.redis_client = None
    
    async def _test_connection(self):
        """Test Redis connection"""
        if self.redis_client:
            self.redis_client.ping()
    
    async def set(self, key: str, value: Any, expiration: int = 3600) -> bool:
        """Set value in cache with expiration"""
        try:
            if not self.redis_client:
                return False
            
            # Serialize complex objects
            if isinstance(value, (dict, list, tuple)):
                value = json.dumps(value, default=str)
            
            self.redis_client.setex(key, expiration, value)
            return True
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.WARNING, LogCategory.SYSTEM,
                f"Cache set failed for key {key}: {str(e)}",
                error=e
            )
            return False
    
    async def get(self, key: str) -> Optional[Any]:
        """Get value from cache"""
        try:
            if not self.redis_client:
                return None
            
            value = self.redis_client.get(key)
            if value is None:
                return None
            
            # Try to deserialize JSON
            try:
                return json.loads(value)
            except (json.JSONDecodeError, TypeError):
                return value
                
        except Exception as e:
            await professional_logger.log(
                LogLevel.WARNING, LogCategory.SYSTEM,
                f"Cache get failed for key {key}: {str(e)}",
                error=e
            )
            return None
    
    async def delete(self, key: str) -> bool:
        """Delete key from cache"""
        try:
            if not self.redis_client:
                return False
            
            return self.redis_client.delete(key) > 0
            
        except Exception:
            return False
    
    async def clear_pattern(self, pattern: str) -> int:
        """Clear keys matching pattern"""
        try:
            if not self.redis_client:
                return 0
            
            keys = self.redis_client.keys(pattern)
            if keys:
                return self.redis_client.delete(*keys)
            return 0
            
        except Exception:
            return 0

class DatabaseConnectionPool:
    """Database connection pooling and optimization"""
    
    def __init__(self):
        self.pool_size = 20
        self.connections = {}
        self.active_connections = 0
        self.connection_stats = {
            "created": 0,
            "reused": 0,
            "closed": 0,
            "errors": 0
        }
    
    async def get_connection(self, connection_id: str = "default"):
        """Get database connection from pool"""
        try:
            if connection_id not in self.connections:
                self.connections[connection_id] = get_database()
                self.connection_stats["created"] += 1
                self.active_connections += 1
            else:
                self.connection_stats["reused"] += 1
            
            return self.connections[connection_id]
            
        except Exception as e:
            self.connection_stats["errors"] += 1
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.DATABASE,
                f"Failed to get database connection: {str(e)}",
                error=e
            )
            raise
    
    async def release_connection(self, connection_id: str = "default"):
        """Release database connection"""
        if connection_id in self.connections:
            # In production, you might want to actually close idle connections
            pass
    
    def get_stats(self) -> Dict[str, Any]:
        """Get connection pool statistics"""
        return {
            "pool_size": self.pool_size,
            "active_connections": self.active_connections,
            "stats": self.connection_stats
        }

class PerformanceMonitor:
    """Performance monitoring and metrics collection"""
    
    def __init__(self):
        self.metrics = {}
        self.start_time = time.time()
    
    async def record_metric(self, metric_name: str, value: Union[int, float], tags: Dict[str, str] = None):
        """Record performance metric"""
        try:
            timestamp = time.time()
            
            if metric_name not in self.metrics:
                self.metrics[metric_name] = []
            
            metric = {
                "timestamp": timestamp,
                "value": value,
                "tags": tags or {}
            }
            
            # Keep only last 1000 metrics per type
            self.metrics[metric_name].append(metric)
            if len(self.metrics[metric_name]) > 1000:
                self.metrics[metric_name] = self.metrics[metric_name][-1000:]
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.WARNING, LogCategory.SYSTEM,
                f"Failed to record metric {metric_name}: {str(e)}",
                error=e
            )
    
    def get_metrics_summary(self) -> Dict[str, Any]:
        """Get performance metrics summary"""
        summary = {}
        
        for metric_name, values in self.metrics.items():
            if not values:
                continue
            
            metric_values = [v["value"] for v in values]
            
            summary[metric_name] = {
                "count": len(metric_values),
                "avg": sum(metric_values) / len(metric_values),
                "min": min(metric_values),
                "max": max(metric_values),
                "recent": metric_values[-10:] if len(metric_values) >= 10 else metric_values
            }
        
        summary["uptime"] = time.time() - self.start_time
        
        return summary

class QueryOptimizer:
    """Database query optimization utilities"""
    
    @staticmethod
    async def create_indexes(collection_name: str, indexes: List[Dict[str, Any]]):
        """Create database indexes for performance"""
        try:
            db = get_database()
            collection = getattr(db, collection_name)
            
            for index in indexes:
                await collection.create_index(list(index.items()))
            
            await professional_logger.log(
                LogLevel.INFO, LogCategory.DATABASE,
                f"Created {len(indexes)} indexes for {collection_name}",
                details={"collection": collection_name, "indexes": len(indexes)}
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.DATABASE,
                f"Failed to create indexes for {collection_name}: {str(e)}",
                error=e
            )
    
    @staticmethod
    async def analyze_slow_queries():
        """Analyze slow queries for optimization"""
        try:
            db = get_database()
            
            # Get profiling data (if enabled)
            profiling_data = await db.command("db.system.profile.find().limit(10)")
            
            slow_queries = []
            for profile in profiling_data.get("cursor", {}).get("firstBatch", []):
                if profile.get("millis", 0) > 100:  # Queries taking more than 100ms
                    slow_queries.append({
                        "ns": profile.get("ns"),
                        "command": profile.get("command"),
                        "duration_ms": profile.get("millis"),
                        "timestamp": profile.get("ts")
                    })
            
            return slow_queries
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.DATABASE,
                f"Failed to analyze slow queries: {str(e)}",
                error=e
            )
            return []

def cache_result(expiration: int = 3600, key_prefix: str = "cache"):
    """Decorator for caching function results"""
    def decorator(func):
        @wraps(func)
        async def wrapper(*args, **kwargs):
            # Generate cache key
            key_data = f"{func.__name__}:{args}:{sorted(kwargs.items())}"
            cache_key = f"{key_prefix}:{hashlib.md5(key_data.encode()).hexdigest()}"
            
            # Try to get from cache
            cached_result = await redis_cache.get(cache_key)
            if cached_result is not None:
                await performance_monitor.record_metric("cache_hit", 1, {"function": func.__name__})
                return cached_result
            
            # Execute function
            start_time = time.time()
            result = await func(*args, **kwargs)
            execution_time = time.time() - start_time
            
            # Cache result
            await redis_cache.set(cache_key, result, expiration)
            
            # Record metrics
            await performance_monitor.record_metric("cache_miss", 1, {"function": func.__name__})
            await performance_monitor.record_metric("function_execution_time", execution_time, {"function": func.__name__})
            
            return result
        
        return wrapper
    return decorator

def monitor_performance(metric_name: str = None):
    """Decorator for monitoring function performance"""
    def decorator(func):
        @wraps(func)
        async def wrapper(*args, **kwargs):
            name = metric_name or f"{func.__module__}.{func.__name__}"
            start_time = time.time()
            
            try:
                result = await func(*args, **kwargs)
                execution_time = time.time() - start_time
                
                await performance_monitor.record_metric(
                    f"{name}.execution_time",
                    execution_time,
                    {"status": "success"}
                )
                await performance_monitor.record_metric(
                    f"{name}.calls",
                    1,
                    {"status": "success"}
                )
                
                return result
                
            except Exception as e:
                execution_time = time.time() - start_time
                
                await performance_monitor.record_metric(
                    f"{name}.execution_time",
                    execution_time,
                    {"status": "error"}
                )
                await performance_monitor.record_metric(
                    f"{name}.calls",
                    1,
                    {"status": "error"}
                )
                
                raise
        
        return wrapper
    return decorator

async def initialize_performance_optimizations():
    """Initialize all performance optimizations"""
    try:
        # Initialize Redis cache
        await redis_cache.initialize()
        
        # Create essential database indexes
        essential_indexes = [
            ("users", [{"email": 1}, {"user_id": 1}, {"created_at": -1}]),
            ("user_sessions", [{"session_id": 1}, {"user_id": 1}, {"expires_at": 1}]),
            ("user_activities", [{"user_id": 1}, {"timestamp": -1}]),
            ("social_media_profiles", [{"user_id": 1}, {"platform": 1}]),
            ("financial_transactions", [{"user_id": 1}, {"created_at": -1}, {"type": 1}]),
            ("email_campaigns", [{"user_id": 1}, {"created_at": -1}]),
            ("admin_system_logs", [{"timestamp": -1}, {"level": 1}, {"category": 1}]),
        ]
        
        for collection_name, indexes in essential_indexes:
            index_specs = []
            for index in indexes:
                index_specs.append(index)
            await query_optimizer.create_indexes(collection_name, index_specs)
        
        await professional_logger.log(
            LogLevel.INFO, LogCategory.SYSTEM,
            "Performance optimizations initialized successfully"
        )
        
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.SYSTEM,
            f"Failed to initialize performance optimizations: {str(e)}",
            error=e
        )

# Global instances
redis_cache = RedisCache()
db_pool = DatabaseConnectionPool()
performance_monitor = PerformanceMonitor()
query_optimizer = QueryOptimizer()