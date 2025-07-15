from fastapi import FastAPI, Request, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse, FileResponse
from fastapi.staticfiles import StaticFiles
import requests
import json
import os
from datetime import datetime
from pathlib import Path

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

# Mount static files
static_dir = Path(__file__).parent.parent / "public"
if static_dir.exists():
    app.mount("/static", StaticFiles(directory=static_dir), name="static")

@app.get("/")
async def root():
    """Serve the main landing page"""
    try:
        # Try to serve the landing page from Laravel backend
        response = requests.get(f"{LARAVEL_BACKEND_URL}/", timeout=5)
        if response.status_code == 200:
            return response.text
    except:
        pass
    
    return {"message": "Mewayz API Proxy is running", "status": "operational"}

# Serve static HTML files from public directory
@app.get("/{file_path:path}")
async def serve_static_files(file_path: str):
    """Serve static HTML files and assets"""
    static_dir = Path(__file__).parent.parent / "public"
    
    # If no file path specified, serve landing page
    if not file_path or file_path == "/":
        try:
            response = requests.get(f"{LARAVEL_BACKEND_URL}/", timeout=5)
            if response.status_code == 200:
                from fastapi.responses import HTMLResponse
                return HTMLResponse(content=response.text)
        except:
            pass
    
    # Handle common static files
    if file_path.endswith('.html'):
        file_full_path = static_dir / file_path
        if file_full_path.exists():
            return FileResponse(file_full_path, media_type="text/html")
    
    # Handle other assets
    if file_path.endswith(('.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.woff', '.woff2', '.ttf')):
        file_full_path = static_dir / file_path
        if file_full_path.exists():
            return FileResponse(file_full_path)
    
    # For HTML files without extension, try adding .html
    if '.' not in file_path:
        html_file_path = static_dir / f"{file_path}.html"
        if html_file_path.exists():
            return FileResponse(html_file_path, media_type="text/html")
    
    # Try to proxy to Laravel backend for other routes
    try:
        response = requests.get(f"{LARAVEL_BACKEND_URL}/{file_path}", timeout=5)
        if response.status_code == 200:
            from fastapi.responses import HTMLResponse
            return HTMLResponse(content=response.text)
    except:
        pass
    
    # Return 404 if file not found
    raise HTTPException(status_code=404, detail=f"File not found: {file_path}")

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