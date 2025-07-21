"""
Security Enhancements
Comprehensive security system with JWT refresh tokens, rate limiting, and input validation
"""
import jwt
import bcrypt
import secrets
import hashlib
import re
from datetime import datetime, timedelta
from typing import Dict, Any, Optional, List
from pydantic import BaseModel, validator
from fastapi import HTTPException, Request
from slowapi import Limiter, _rate_limit_exceeded_handler
from slowapi.util import get_remote_address
from slowapi.errors import RateLimitExceeded
import httpx

from core.database import get_database
from core.professional_logger import professional_logger, LogLevel, LogCategory

# Rate limiting configuration
limiter = Limiter(key_func=get_remote_address)

class SecurityConfig:
    """Security configuration constants"""
    JWT_SECRET_KEY = secrets.token_urlsafe(32)
    JWT_ALGORITHM = "HS256"
    ACCESS_TOKEN_EXPIRE_MINUTES = 30
    REFRESH_TOKEN_EXPIRE_DAYS = 7
    
    # Password requirements
    MIN_PASSWORD_LENGTH = 8
    MAX_PASSWORD_LENGTH = 128
    REQUIRE_UPPERCASE = True
    REQUIRE_LOWERCASE = True
    REQUIRE_DIGITS = True
    REQUIRE_SPECIAL = True
    
    # Rate limiting
    LOGIN_RATE_LIMIT = "5/minute"
    API_RATE_LIMIT = "100/minute"
    ADMIN_RATE_LIMIT = "200/minute"

class TokenData(BaseModel):
    """Token data model with validation"""
    user_id: str
    email: str
    role: str = "user"
    permissions: List[str] = []
    token_type: str = "access"  # access or refresh

class PasswordValidator:
    """Advanced password validation"""
    
    @staticmethod
    def validate_password(password: str) -> Dict[str, Any]:
        """Validate password against security requirements"""
        errors = []
        
        if len(password) < SecurityConfig.MIN_PASSWORD_LENGTH:
            errors.append(f"Password must be at least {SecurityConfig.MIN_PASSWORD_LENGTH} characters")
        
        if len(password) > SecurityConfig.MAX_PASSWORD_LENGTH:
            errors.append(f"Password must be no more than {SecurityConfig.MAX_PASSWORD_LENGTH} characters")
        
        if SecurityConfig.REQUIRE_UPPERCASE and not re.search(r'[A-Z]', password):
            errors.append("Password must contain at least one uppercase letter")
        
        if SecurityConfig.REQUIRE_LOWERCASE and not re.search(r'[a-z]', password):
            errors.append("Password must contain at least one lowercase letter")
        
        if SecurityConfig.REQUIRE_DIGITS and not re.search(r'\d', password):
            errors.append("Password must contain at least one digit")
        
        if SecurityConfig.REQUIRE_SPECIAL and not re.search(r'[!@#$%^&*(),.?":{}|<>]', password):
            errors.append("Password must contain at least one special character")
        
        # Check for common weak passwords
        common_passwords = [
            "password", "123456", "123456789", "qwerty", "abc123", 
            "password123", "admin", "letmein", "welcome", "monkey"
        ]
        if password.lower() in common_passwords:
            errors.append("Password is too common")
        
        return {
            "valid": len(errors) == 0,
            "errors": errors,
            "strength": PasswordValidator._calculate_strength(password)
        }
    
    @staticmethod
    def _calculate_strength(password: str) -> str:
        """Calculate password strength"""
        score = 0
        
        if len(password) >= 8:
            score += 1
        if len(password) >= 12:
            score += 1
        if re.search(r'[A-Z]', password):
            score += 1
        if re.search(r'[a-z]', password):
            score += 1
        if re.search(r'\d', password):
            score += 1
        if re.search(r'[!@#$%^&*(),.?":{}|<>]', password):
            score += 1
        if len(password) >= 16:
            score += 1
        
        if score <= 2:
            return "weak"
        elif score <= 4:
            return "medium"
        elif score <= 6:
            return "strong"
        else:
            return "very_strong"

class PasswordHasher:
    """Secure password hashing using bcrypt"""
    
    @staticmethod
    def hash_password(password: str) -> str:
        """Hash password using bcrypt"""
        salt = bcrypt.gensalt(rounds=12)
        hashed = bcrypt.hashpw(password.encode('utf-8'), salt)
        return hashed.decode('utf-8')
    
    @staticmethod
    def verify_password(password: str, hashed: str) -> bool:
        """Verify password against hash"""
        return bcrypt.checkpw(password.encode('utf-8'), hashed.encode('utf-8'))

class JWTManager:
    """JWT token management with refresh tokens"""
    
    @staticmethod
    def create_access_token(data: TokenData) -> str:
        """Create JWT access token"""
        to_encode = data.dict()
        expire = datetime.utcnow() + timedelta(minutes=SecurityConfig.ACCESS_TOKEN_EXPIRE_MINUTES)
        to_encode.update({"exp": expire, "type": "access"})
        
        encoded_jwt = jwt.encode(
            to_encode, 
            SecurityConfig.JWT_SECRET_KEY, 
            algorithm=SecurityConfig.JWT_ALGORITHM
        )
        return encoded_jwt
    
    @staticmethod
    def create_refresh_token(data: TokenData) -> str:
        """Create JWT refresh token"""
        to_encode = data.dict()
        expire = datetime.utcnow() + timedelta(days=SecurityConfig.REFRESH_TOKEN_EXPIRE_DAYS)
        to_encode.update({"exp": expire, "type": "refresh"})
        
        encoded_jwt = jwt.encode(
            to_encode,
            SecurityConfig.JWT_SECRET_KEY,
            algorithm=SecurityConfig.JWT_ALGORITHM
        )
        return encoded_jwt
    
    @staticmethod
    def verify_token(token: str, token_type: str = "access") -> Optional[Dict[str, Any]]:
        """Verify JWT token"""
        try:
            payload = jwt.decode(
                token,
                SecurityConfig.JWT_SECRET_KEY,
                algorithms=[SecurityConfig.JWT_ALGORITHM]
            )
            
            if payload.get("type") != token_type:
                return None
                
            return payload
            
        except jwt.ExpiredSignatureError:
            return None
        except jwt.InvalidTokenError:
            return None
    
    @staticmethod
    async def revoke_token(token: str, user_id: str):
        """Add token to revocation list"""
        try:
            db = get_database()
            
            revoked_token = {
                "token_hash": hashlib.sha256(token.encode()).hexdigest(),
                "user_id": user_id,
                "revoked_at": datetime.utcnow(),
                "expires_at": datetime.utcnow() + timedelta(days=SecurityConfig.REFRESH_TOKEN_EXPIRE_DAYS)
            }
            
            await db.revoked_tokens.insert_one(revoked_token)
            
            await professional_logger.log(
                LogLevel.INFO, LogCategory.SECURITY,
                f"Token revoked for user {user_id}",
                user_id=user_id
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.SECURITY,
                f"Failed to revoke token: {str(e)}",
                error=e
            )
    
    @staticmethod
    async def is_token_revoked(token: str) -> bool:
        """Check if token is revoked"""
        try:
            db = get_database()
            token_hash = hashlib.sha256(token.encode()).hexdigest()
            
            revoked = await db.revoked_tokens.find_one({
                "token_hash": token_hash,
                "expires_at": {"$gt": datetime.utcnow()}
            })
            
            return revoked is not None
            
        except Exception:
            return False

class InputSanitizer:
    """Input validation and sanitization"""
    
    @staticmethod
    def sanitize_string(input_str: str, max_length: int = 255) -> str:
        """Sanitize string input"""
        if not input_str:
            return ""
        
        # Remove null bytes
        sanitized = input_str.replace('\x00', '')
        
        # Trim whitespace
        sanitized = sanitized.strip()
        
        # Limit length
        if len(sanitized) > max_length:
            sanitized = sanitized[:max_length]
        
        return sanitized
    
    @staticmethod
    def validate_email(email: str) -> bool:
        """Validate email format"""
        pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        return bool(re.match(pattern, email))
    
    @staticmethod
    def sanitize_html(html_content: str) -> str:
        """Basic HTML sanitization (remove scripts)"""
        import html
        
        # HTML escape
        sanitized = html.escape(html_content)
        
        # Remove script tags and their content
        sanitized = re.sub(r'<script.*?</script>', '', sanitized, flags=re.IGNORECASE | re.DOTALL)
        
        # Remove javascript: URLs
        sanitized = re.sub(r'javascript:', '', sanitized, flags=re.IGNORECASE)
        
        return sanitized
    
    @staticmethod
    def validate_sql_input(input_str: str) -> bool:
        """Check for potential SQL injection patterns"""
        sql_patterns = [
            r"('|(\\')|(;)|(\\';)|(\\\\))",
            r"((\%3D)|(=))[^\n]*((\%27)|(\')|((\%3B)|(;)))",
            r"\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))",
            r"((\%27)|(\'))union",
            r"exec(\+|\s)+x?p_\w+",
            r"UNION(\s|\+)+SELECT",
            r"INSERT(\s|\+)+INTO",
            r"UPDATE(\s|\+)+.+SET",
            r"DELETE(\s|\+)+FROM"
        ]
        
        for pattern in sql_patterns:
            if re.search(pattern, input_str, re.IGNORECASE):
                return False
        
        return True

class SecurityMiddleware:
    """Security middleware for request validation"""
    
    @staticmethod
    async def validate_request_security(request: Request) -> Dict[str, Any]:
        """Comprehensive request security validation"""
        security_checks = {
            "ip_validated": True,
            "user_agent_valid": True,
            "content_type_safe": True,
            "headers_secure": True,
            "rate_limit_ok": True
        }
        
        # Check for suspicious IP patterns
        client_ip = get_remote_address(request)
        if await SecurityMiddleware._is_suspicious_ip(client_ip):
            security_checks["ip_validated"] = False
            await professional_logger.log_security_event(
                "suspicious_ip_detected", 
                ip_address=client_ip,
                details={"endpoint": str(request.url)}
            )
        
        # Validate User-Agent
        user_agent = request.headers.get("user-agent", "")
        if not user_agent or await SecurityMiddleware._is_bot_user_agent(user_agent):
            security_checks["user_agent_valid"] = False
        
        # Check content type for POST requests
        if request.method in ["POST", "PUT", "PATCH"]:
            content_type = request.headers.get("content-type", "")
            if not SecurityMiddleware._is_safe_content_type(content_type):
                security_checks["content_type_safe"] = False
        
        return security_checks
    
    @staticmethod
    async def _is_suspicious_ip(ip: str) -> bool:
        """Check if IP is suspicious (basic implementation)"""
        # Check against known threat feeds (simplified)
        suspicious_patterns = [
            "10.0.0.",  # Example patterns
            "192.168.1.",  # Local IPs in production might be suspicious
        ]
        
        return any(ip.startswith(pattern) for pattern in suspicious_patterns)
    
    @staticmethod
    async def _is_bot_user_agent(user_agent: str) -> bool:
        """Check for bot user agents"""
        bot_patterns = [
            "bot", "crawler", "spider", "scraper", "curl", "wget", 
            "python-requests", "postman", "insomnia"
        ]
        
        return any(pattern in user_agent.lower() for pattern in bot_patterns)
    
    @staticmethod
    def _is_safe_content_type(content_type: str) -> bool:
        """Check if content type is safe"""
        safe_types = [
            "application/json",
            "application/x-www-form-urlencoded",
            "multipart/form-data",
            "text/plain"
        ]
        
        return any(content_type.startswith(safe_type) for safe_type in safe_types)

class CSRFProtection:
    """CSRF token generation and validation"""
    
    @staticmethod
    def generate_csrf_token() -> str:
        """Generate CSRF token"""
        return secrets.token_urlsafe(32)
    
    @staticmethod
    def validate_csrf_token(token: str, session_token: str) -> bool:
        """Validate CSRF token"""
        return secrets.compare_digest(token, session_token)

class SessionManager:
    """Secure session management"""
    
    @staticmethod
    async def create_session(user_id: str, ip_address: str, user_agent: str) -> str:
        """Create secure session"""
        try:
            db = get_database()
            session_id = secrets.token_urlsafe(32)
            
            session = {
                "session_id": session_id,
                "user_id": user_id,
                "ip_address": ip_address,
                "user_agent": user_agent,
                "created_at": datetime.utcnow(),
                "last_activity": datetime.utcnow(),
                "expires_at": datetime.utcnow() + timedelta(hours=24),
                "active": True
            }
            
            await db.user_sessions.insert_one(session)
            
            await professional_logger.log_security_event(
                "session_created",
                user_id=user_id,
                ip_address=ip_address,
                details={"session_id": session_id}
            )
            
            return session_id
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.SECURITY,
                f"Failed to create session: {str(e)}",
                error=e
            )
            return ""
    
    @staticmethod
    async def validate_session(session_id: str, ip_address: str) -> Optional[Dict[str, Any]]:
        """Validate session"""
        try:
            db = get_database()
            
            session = await db.user_sessions.find_one({
                "session_id": session_id,
                "active": True,
                "expires_at": {"$gt": datetime.utcnow()}
            })
            
            if not session:
                return None
            
            # Check IP consistency (optional, can be disabled for mobile users)
            # if session["ip_address"] != ip_address:
            #     return None
            
            # Update last activity
            await db.user_sessions.update_one(
                {"session_id": session_id},
                {"$set": {"last_activity": datetime.utcnow()}}
            )
            
            return session
            
        except Exception:
            return None
    
    @staticmethod
    async def invalidate_session(session_id: str, user_id: str):
        """Invalidate session"""
        try:
            db = get_database()
            
            await db.user_sessions.update_one(
                {"session_id": session_id},
                {"$set": {"active": False, "invalidated_at": datetime.utcnow()}}
            )
            
            await professional_logger.log_security_event(
                "session_invalidated",
                user_id=user_id,
                details={"session_id": session_id}
            )
            
        except Exception as e:
            await professional_logger.log(
                LogLevel.ERROR, LogCategory.SECURITY,
                f"Failed to invalidate session: {str(e)}",
                error=e
            )

# Global instances
password_validator = PasswordValidator()
password_hasher = PasswordHasher()
jwt_manager = JWTManager()
input_sanitizer = InputSanitizer()
security_middleware = SecurityMiddleware()
csrf_protection = CSRFProtection()
session_manager = SessionManager()