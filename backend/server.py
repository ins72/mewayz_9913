from fastapi import FastAPI, Request, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
import requests
import json
import os
from datetime import datetime

app = FastAPI(title="Mewayz API Proxy", version="1.0.0")

# Configure CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Laravel backend URL (PHP-FPM will run on port 8002)
LARAVEL_BACKEND_URL = "http://localhost:8002"

@app.get("/")
async def root():
    return {"message": "Mewayz API Proxy is running", "status": "operational"}

@app.get("/health")
async def health_check():
    try:
        # Check if Laravel backend is responding
        response = requests.get(f"{LARAVEL_BACKEND_URL}/", timeout=5)
        laravel_status = "healthy" if response.status_code < 400 else "unhealthy"
    except:
        laravel_status = "unreachable"
    
    return {
        "api_proxy": "healthy",
        "laravel_backend": laravel_status,
        "timestamp": str(datetime.now())
    }

# Proxy all API calls to Laravel backend
@app.api_route("/api/{path:path}", methods=["GET", "POST", "PUT", "DELETE", "PATCH"])
async def proxy_to_laravel(path: str, request: Request):
    try:
        # Get request data
        method = request.method
        headers = dict(request.headers)
        
        # Remove host header to avoid conflicts
        headers.pop('host', None)
        headers.pop('content-length', None)
        
        # Get request body for POST/PUT/PATCH requests
        body = None
        if method in ["POST", "PUT", "PATCH"]:
            body = await request.body()
        
        # Make request to Laravel backend
        url = f"{LARAVEL_BACKEND_URL}/api/{path}"
        
        response = requests.request(
            method=method,
            url=url,
            headers=headers,
            data=body,
            params=dict(request.query_params),
            timeout=30,
            allow_redirects=False
        )
        
        return JSONResponse(
            content=response.json() if response.headers.get('content-type', '').startswith('application/json') else response.text,
            status_code=response.status_code,
            headers=dict(response.headers)
        )
        
    except requests.exceptions.RequestException as e:
        raise HTTPException(status_code=503, detail=f"Laravel backend unavailable: {str(e)}")
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Proxy error: {str(e)}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8001)