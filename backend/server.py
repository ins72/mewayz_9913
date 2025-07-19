from fastapi import FastAPI, HTTPException
from fastapi.responses import RedirectResponse
import httpx
import uvicorn

app = FastAPI(title="Mewayz Bridge API", version="1.0.0")

# Laravel backend URL
LARAVEL_URL = "http://localhost:8001"

@app.get("/")
async def root():
    return {"message": "Mewayz Bridge API - Redirecting to Laravel Backend", "laravel_url": LARAVEL_URL}

@app.get("/health")
async def health_check():
    """Health check endpoint"""
    try:
        async with httpx.AsyncClient() as client:
            response = await client.get(f"{LARAVEL_URL}/api/health")
            if response.status_code == 200:
                return response.json()
            else:
                raise HTTPException(status_code=502, detail="Laravel backend unhealthy")
    except Exception as e:
        raise HTTPException(status_code=502, detail=f"Laravel backend unreachable: {str(e)}")

# Proxy all API requests to Laravel
@app.api_route("/api/{path:path}", methods=["GET", "POST", "PUT", "DELETE", "PATCH"])
async def proxy_to_laravel(path: str, request):
    """Proxy all API requests to Laravel backend"""
    try:
        async with httpx.AsyncClient() as client:
            # Forward the request to Laravel
            response = await client.request(
                method=request.method,
                url=f"{LARAVEL_URL}/api/{path}",
                headers=dict(request.headers),
                content=await request.body()
            )
            return response.json() if response.headers.get("content-type") == "application/json" else response.text
    except Exception as e:
        raise HTTPException(status_code=502, detail=f"Error proxying to Laravel: {str(e)}")

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8002)