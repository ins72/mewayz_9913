from fastapi import FastAPI, HTTPException, Request
from fastapi.responses import JSONResponse
from fastapi.middleware.cors import CORSMiddleware
import httpx
import asyncio
from typing import Any, Dict

app = FastAPI(title="Mewayz Proxy API", version="1.0.0")

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000", "http://localhost:8001"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Laravel backend URL
LARAVEL_BACKEND_URL = "http://localhost:8002"

@app.get("/api/health")
async def health_check():
    return {
        "success": True,
        "message": "FastAPI Proxy is healthy",
        "laravel_backend": LARAVEL_BACKEND_URL,
        "timestamp": "2025-07-19T06:15:00.000Z"
    }

@app.get("/api/test")
async def api_test():
    return {
        "message": "Mewayz API is working!",
        "status": "success",
        "version": "1.0.0",
        "timestamp": "2025-07-19T06:15:00.000Z"
    }

# Simple authentication endpoints without Laravel
@app.post("/api/auth/login")
async def login(request: Request):
    try:
        body = await request.json()
        email = body.get("email")
        password = body.get("password")
        
        # Simple authentication check
        if email == "tmonnens@outlook.com" and password == "Voetballen5":
            return {
                "success": True,
                "message": "Login successful",
                "user": {
                    "id": 1,
                    "name": "Admin User",
                    "email": email,
                    "role": 1,
                    "email_verified": True
                },
                "token": "mock-jwt-token-for-admin-user"
            }
        else:
            raise HTTPException(status_code=401, detail="Invalid credentials")
    
    except Exception as e:
        raise HTTPException(status_code=400, detail="Invalid request")

@app.post("/api/auth/register")
async def register(request: Request):
    return {
        "success": True,
        "message": "Registration successful",
        "user": {
            "id": 2,
            "name": "New User",
            "email": "new@example.com",
            "role": 0,
            "email_verified": False
        }
    }

@app.get("/api/auth/me")
async def get_user(request: Request):
    # Check for authorization header
    auth_header = request.headers.get("authorization")
    if not auth_header or not auth_header.startswith("Bearer "):
        raise HTTPException(status_code=401, detail="Unauthorized")
    
    return {
        "success": True,
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "tmonnens@outlook.com",
            "role": 1,
            "email_verified": True
        }
    }

# Mock endpoints for testing
@app.get("/api/admin/dashboard")
async def admin_dashboard(request: Request):
    auth_header = request.headers.get("authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Unauthorized")
        
    return {
        "success": True,
        "data": {
            "user_metrics": {
                "total_users": 156,
                "active_users": 89,
                "new_users_today": 5,
                "premium_users": 23
            },
            "revenue_metrics": {
                "total_revenue": 45230,
                "monthly_revenue": 12400,
                "growth_rate": 15.3
            },
            "system_health": {
                "uptime": "99.9%",
                "response_time": "120ms",
                "error_rate": "0.1%"
            }
        }
    }

@app.get("/api/websites")
async def get_websites(request: Request):
    auth_header = request.headers.get("authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Unauthorized")
        
    return {
        "success": True,
        "data": [
            {
                "id": 1,
                "name": "My Business Site",
                "domain": "mybusiness.mewayz.com",
                "status": "published",
                "created_at": "2025-07-15"
            }
        ]
    }

@app.get("/api/email-marketing/campaigns")
async def get_campaigns(request: Request):
    auth_header = request.headers.get("authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Unauthorized")
        
    return {
        "success": True,
        "campaigns": [
            {
                "id": 1,
                "name": "Welcome Series",
                "status": "active",
                "sent_count": 1250,
                "open_rate": 24.5
            }
        ]
    }

@app.get("/api/crm/contacts")  
async def get_contacts(request: Request):
    auth_header = request.headers.get("authorization")
    if not auth_header:
        raise HTTPException(status_code=401, detail="Unauthorized")
        
    return {
        "success": True,
        "data": {
            "contacts": [
                {
                    "id": 1,
                    "name": "John Smith",
                    "email": "john@example.com",
                    "company": "Tech Corp",
                    "status": "active"
                }
            ]
        }
    }

# Catch-all proxy route for Laravel backend
@app.api_route("/api/{path:path}", methods=["GET", "POST", "PUT", "DELETE", "PATCH"])
async def proxy_to_laravel(request: Request, path: str):
    """
    Proxy requests to Laravel backend when available
    """
    try:
        # For now, just return mock success response
        return {
            "success": True,
            "message": f"Mock response for {path}",
            "data": {"status": "proxy_active"}
        }
    except Exception as e:
        return {
            "success": False,
            "message": f"Proxy error for {path}",
            "error": str(e)
        }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)