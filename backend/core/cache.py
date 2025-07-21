"""
Cache Manager with Redis
High-performance caching for external API responses and database queries
"""

import json
import pickle
from typing import Any, Dict, Optional, List
from datetime import datetime, timedelta
import asyncio
from core.logging import admin_logger

try:
    import redis.asyncio as redis
    REDIS_AVAILABLE = True
except ImportError:
    REDIS_AVAILABLE = False
    redis = None

class CacheManager:
    """High-performance cache manager using Redis"""
    
    def __init__(self):
        self.redis_client = None
        self.fallback_cache = {}  # In-memory fallback
        self.cache_stats = {
            "hits": 0,
            "misses": 0,
            "errors": 0
        }
        
    async def initialize(self):
        """Initialize Redis connection"""
        if REDIS_AVAILABLE:
            try:
                # Connect to Redis
                self.redis_client = redis.Redis(
                    host='localhost',
                    port=6379,
                    db=0,
                    decode_responses=True,
                    socket_timeout=5.0,
                    socket_connect_timeout=5.0,
                    retry_on_timeout=True,
                    health_check_interval=30
                )
                
                # Test connection
                await self.redis_client.ping()
                admin_logger.log_system_event("REDIS_CONNECTED", {
                    "host": "localhost",
                    "port": 6379
                })
                
            except Exception as e:
                admin_logger.log_system_event("REDIS_CONNECTION_FAILED", {
                    "error": str(e)
                }, "WARNING")
                self.redis_client = None
        
        if not self.redis_client:
            admin_logger.log_system_event("USING_MEMORY_CACHE", {
                "reason": "Redis unavailable"
            }, "WARNING")
    
    async def close(self):
        """Close Redis connection"""
        if self.redis_client:
            await self.redis_client.close()
    
    async def get(self, key: str) -> Optional[Any]:
        """Get value from cache"""
        try:
            if self.redis_client:
                value = await self.redis_client.get(f"mewayz:{key}")
                if value is not None:
                    self.cache_stats["hits"] += 1
                    try:
                        return json.loads(value)
                    except json.JSONDecodeError:
                        # Try pickle for complex objects
                        return pickle.loads(value.encode('latin-1'))
                else:
                    self.cache_stats["misses"] += 1
                    return None
            else:
                # Fallback to memory cache
                if key in self.fallback_cache:
                    entry = self.fallback_cache[key]
                    if entry["expires_at"] > datetime.utcnow():
                        self.cache_stats["hits"] += 1
                        return entry["data"]
                    else:
                        del self.fallback_cache[key]
                
                self.cache_stats["misses"] += 1
                return None
                
        except Exception as e:
            self.cache_stats["errors"] += 1
            admin_logger.log_system_event("CACHE_GET_ERROR", {
                "key": key,
                "error": str(e)
            }, "WARNING")
            return None
    
    async def set(self, key: str, value: Any, expire_seconds: int = 3600) -> bool:
        """Set value in cache with expiration"""
        try:
            if self.redis_client:
                try:
                    serialized_value = json.dumps(value)
                except (TypeError, ValueError):
                    # Use pickle for complex objects
                    serialized_value = pickle.dumps(value).decode('latin-1')
                
                await self.redis_client.setex(
                    f"mewayz:{key}",
                    expire_seconds,
                    serialized_value
                )
                return True
            else:
                # Fallback to memory cache
                self.fallback_cache[key] = {
                    "data": value,
                    "expires_at": datetime.utcnow() + timedelta(seconds=expire_seconds)
                }
                
                # Clean expired entries periodically
                await self._cleanup_memory_cache()
                return True
                
        except Exception as e:
            self.cache_stats["errors"] += 1
            admin_logger.log_system_event("CACHE_SET_ERROR", {
                "key": key,
                "error": str(e)
            }, "WARNING")
            return False
    
    async def delete(self, key: str) -> bool:
        """Delete key from cache"""
        try:
            if self.redis_client:
                result = await self.redis_client.delete(f"mewayz:{key}")
                return result > 0
            else:
                if key in self.fallback_cache:
                    del self.fallback_cache[key]
                    return True
                return False
                
        except Exception as e:
            self.cache_stats["errors"] += 1
            admin_logger.log_system_event("CACHE_DELETE_ERROR", {
                "key": key,
                "error": str(e)
            }, "WARNING")
            return False
    
    async def exists(self, key: str) -> bool:
        """Check if key exists in cache"""
        try:
            if self.redis_client:
                result = await self.redis_client.exists(f"mewayz:{key}")
                return result > 0
            else:
                if key in self.fallback_cache:
                    entry = self.fallback_cache[key]
                    if entry["expires_at"] > datetime.utcnow():
                        return True
                    else:
                        del self.fallback_cache[key]
                return False
                
        except Exception as e:
            admin_logger.log_system_event("CACHE_EXISTS_ERROR", {
                "key": key,
                "error": str(e)
            }, "WARNING")
            return False
    
    async def get_many(self, keys: List[str]) -> Dict[str, Any]:
        """Get multiple values from cache"""
        results = {}
        
        if self.redis_client:
            try:
                redis_keys = [f"mewayz:{key}" for key in keys]
                values = await self.redis_client.mget(redis_keys)
                
                for i, value in enumerate(values):
                    if value is not None:
                        try:
                            results[keys[i]] = json.loads(value)
                        except json.JSONDecodeError:
                            results[keys[i]] = pickle.loads(value.encode('latin-1'))
                        self.cache_stats["hits"] += 1
                    else:
                        self.cache_stats["misses"] += 1
                        
            except Exception as e:
                self.cache_stats["errors"] += 1
                admin_logger.log_system_event("CACHE_MGET_ERROR", {
                    "keys": keys,
                    "error": str(e)
                }, "WARNING")
        else:
            # Fallback to individual gets
            for key in keys:
                value = await self.get(key)
                if value is not None:
                    results[key] = value
        
        return results
    
    async def set_many(self, data: Dict[str, Any], expire_seconds: int = 3600) -> bool:
        """Set multiple values in cache"""
        if self.redis_client:
            try:
                pipe = self.redis_client.pipeline()
                for key, value in data.items():
                    try:
                        serialized_value = json.dumps(value)
                    except (TypeError, ValueError):
                        serialized_value = pickle.dumps(value).decode('latin-1')
                    
                    pipe.setex(f"mewayz:{key}", expire_seconds, serialized_value)
                
                await pipe.execute()
                return True
                
            except Exception as e:
                self.cache_stats["errors"] += 1
                admin_logger.log_system_event("CACHE_MSET_ERROR", {
                    "keys": list(data.keys()),
                    "error": str(e)
                }, "WARNING")
                return False
        else:
            # Fallback to individual sets
            success = True
            for key, value in data.items():
                if not await self.set(key, value, expire_seconds):
                    success = False
            return success
    
    async def clear_pattern(self, pattern: str) -> int:
        """Clear keys matching pattern"""
        try:
            if self.redis_client:
                keys = await self.redis_client.keys(f"mewayz:{pattern}")
                if keys:
                    return await self.redis_client.delete(*keys)
                return 0
            else:
                # Pattern matching for memory cache
                import fnmatch
                matching_keys = [
                    key for key in self.fallback_cache.keys()
                    if fnmatch.fnmatch(key, pattern)
                ]
                for key in matching_keys:
                    del self.fallback_cache[key]
                return len(matching_keys)
                
        except Exception as e:
            self.cache_stats["errors"] += 1
            admin_logger.log_system_event("CACHE_CLEAR_PATTERN_ERROR", {
                "pattern": pattern,
                "error": str(e)
            }, "WARNING")
            return 0
    
    async def increment(self, key: str, amount: int = 1, expire_seconds: int = 3600) -> int:
        """Increment counter in cache"""
        try:
            if self.redis_client:
                result = await self.redis_client.incr(f"mewayz:{key}", amount)
                await self.redis_client.expire(f"mewayz:{key}", expire_seconds)
                return result
            else:
                # Memory cache increment
                current_value = await self.get(key) or 0
                new_value = current_value + amount
                await self.set(key, new_value, expire_seconds)
                return new_value
                
        except Exception as e:
            self.cache_stats["errors"] += 1
            admin_logger.log_system_event("CACHE_INCREMENT_ERROR", {
                "key": key,
                "error": str(e)
            }, "WARNING")
            return 0
    
    async def get_stats(self) -> Dict[str, Any]:
        """Get cache statistics"""
        total_requests = self.cache_stats["hits"] + self.cache_stats["misses"]
        hit_rate = (self.cache_stats["hits"] / total_requests * 100) if total_requests > 0 else 0
        
        stats = {
            "hits": self.cache_stats["hits"],
            "misses": self.cache_stats["misses"],
            "errors": self.cache_stats["errors"],
            "hit_rate": round(hit_rate, 2),
            "total_requests": total_requests,
            "backend": "redis" if self.redis_client else "memory"
        }
        
        if self.redis_client:
            try:
                info = await self.redis_client.info()
                stats["redis_info"] = {
                    "connected_clients": info.get("connected_clients", 0),
                    "used_memory": info.get("used_memory", 0),
                    "keyspace_hits": info.get("keyspace_hits", 0),
                    "keyspace_misses": info.get("keyspace_misses", 0)
                }
            except Exception as e:
                stats["redis_error"] = str(e)
        else:
            stats["memory_cache_size"] = len(self.fallback_cache)
        
        return stats
    
    async def health_check(self) -> bool:
        """Check cache health"""
        try:
            if self.redis_client:
                await self.redis_client.ping()
                return True
            else:
                # Memory cache is always "healthy"
                return True
        except Exception:
            return False
    
    async def _cleanup_memory_cache(self):
        """Clean up expired entries from memory cache"""
        if len(self.fallback_cache) > 1000:  # Only cleanup when cache gets large
            now = datetime.utcnow()
            expired_keys = [
                key for key, entry in self.fallback_cache.items()
                if entry["expires_at"] <= now
            ]
            for key in expired_keys:
                del self.fallback_cache[key]
    
    # Specialized caching methods for common use cases
    
    async def cache_external_api_response(self, api_name: str, endpoint: str, 
                                        response_data: Any, expire_minutes: int = 15) -> bool:
        """Cache external API response"""
        cache_key = f"api:{api_name}:{endpoint}"
        cached_data = {
            "data": response_data,
            "cached_at": datetime.utcnow().isoformat(),
            "api": api_name,
            "endpoint": endpoint
        }
        return await self.set(cache_key, cached_data, expire_minutes * 60)
    
    async def get_cached_api_response(self, api_name: str, endpoint: str) -> Optional[Dict[str, Any]]:
        """Get cached external API response"""
        cache_key = f"api:{api_name}:{endpoint}"
        return await self.get(cache_key)
    
    async def cache_database_query(self, query_hash: str, result: Any, expire_minutes: int = 30) -> bool:
        """Cache database query result"""
        cache_key = f"db:{query_hash}"
        cached_data = {
            "result": result,
            "cached_at": datetime.utcnow().isoformat(),
            "query_hash": query_hash
        }
        return await self.set(cache_key, cached_data, expire_minutes * 60)
    
    async def get_cached_database_query(self, query_hash: str) -> Optional[Any]:
        """Get cached database query result"""
        cache_key = f"db:{query_hash}"
        cached_data = await self.get(cache_key)
        return cached_data.get("result") if cached_data else None
    
    async def cache_user_session(self, user_id: str, session_data: Dict[str, Any], expire_hours: int = 24) -> bool:
        """Cache user session data"""
        cache_key = f"session:{user_id}"
        return await self.set(cache_key, session_data, expire_hours * 3600)
    
    async def get_user_session(self, user_id: str) -> Optional[Dict[str, Any]]:
        """Get cached user session data"""
        cache_key = f"session:{user_id}"
        return await self.get(cache_key)
    
    async def invalidate_user_cache(self, user_id: str) -> int:
        """Invalidate all cache entries for a user"""
        return await self.clear_pattern(f"*{user_id}*")

# Global cache manager instance
cache_manager = CacheManager()