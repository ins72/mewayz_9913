#!/usr/bin/env python3
"""
Backend Startup Script
"""

import uvicorn
from simple_backend import app

if __name__ == "__main__":
    print("🚀 Starting Mewayz Backend Server...")
    print("📍 MongoDB: localhost:5000")
    print("🌐 Backend: http://localhost:8000")
    print("📊 Health: http://localhost:8000/health")
    
    uvicorn.run(
        app,
        host="0.0.0.0",
        port=8000,
        log_level="info",
        access_log=True
    ) 