# JWT Authentication and Authorization Module for Mewayz Platform
import os
from datetime import datetime, timedelta
from typing import Optional, List, Dict, Any
from fastapi import Depends, HTTPException, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from jose import JWTError, jwt
from pymongo import MongoClient

# Environment variables
MONGO_URL = os.getenv("MONGO_URL", "mongodb://localhost:27017")
SECRET_KEY = os.getenv("SECRET_KEY", "your-super-secret-jwt-key-change-in-production")
ALGORITHM = os.getenv("ALGORITHM", "HS256")
ACCESS_TOKEN_EXPIRE_MINUTES = int(os.getenv("ACCESS_TOKEN_EXPIRE_MINUTES", "1440"))  # 24 hours

# MongoDB setup
client = MongoClient(MONGO_URL)
db = client.mewayz
users_collection = db.users

# Security scheme
security = HTTPBearer()

def create_access_token(data: dict) -> str:
    """Create a new JWT access token"""
    to_encode = data.copy()
    expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

async def verify_token(credentials: HTTPAuthorizationCredentials = Depends(security)) -> str:
    """Verify JWT token and return email"""
    try:
        payload = jwt.decode(credentials.credentials, SECRET_KEY, algorithms=[ALGORITHM])
        email: str = payload.get("sub")
        if email is None:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid authentication credentials",
                headers={"WWW-Authenticate": "Bearer"},
            )
        return email
    except JWTError as e:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail=f"Could not validate credentials: {str(e)}",
            headers={"WWW-Authenticate": "Bearer"},
        )

async def get_current_user(email: str = Depends(verify_token)) -> Dict[str, Any]:
    """Get current user from database"""
    user = await users_collection.find_one({"email": email})
    if user is None:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="User not found"
        )
    # Convert ObjectId to string for JSON serialization
    user["id"] = str(user["_id"])
    return user

async def get_current_admin_user(current_user: dict = Depends(get_current_user)) -> Dict[str, Any]:
    """Verify current user is admin"""
    if current_user.get("role") != "admin":
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin access required"
        )
    return current_user

async def require_permission(required_permission: str):
    """Check if user has required permission"""
    def permission_checker(current_user: dict = Depends(get_current_user)):
        user_permissions = current_user.get("permissions", [])
        if required_permission not in user_permissions and current_user.get("role") != "admin":
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail=f"Permission '{required_permission}' required"
            )
        return current_user
    return permission_checker

async def require_role(required_role: str):
    """Check if user has required role"""
    def role_checker(current_user: dict = Depends(get_current_user)):
        if current_user.get("role") != required_role and current_user.get("role") != "admin":
            raise HTTPException(
                status_code=status.HTTP_403_FORBIDDEN,
                detail=f"Role '{required_role}' required"
            )
        return current_user
    return role_checker

async def verify_workspace_access(workspace_id: str, current_user: dict = Depends(get_current_user)):
    """Verify user has access to workspace"""
    user_workspaces = current_user.get("workspaces", [])
    if workspace_id not in user_workspaces and current_user.get("role") != "admin":
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Workspace access denied"
        )
    return current_user

# Optional dependencies for testing
async def get_current_user_optional(credentials: HTTPAuthorizationCredentials = Depends(security)) -> Optional[Dict[str, Any]]:
    """Get current user without failing if token is invalid (for optional auth)"""
    try:
        email = await verify_token(credentials)
        user = await users_collection.find_one({"email": email})
        if user:
            user["id"] = str(user["_id"])
            return user
    except:
        pass
    return None