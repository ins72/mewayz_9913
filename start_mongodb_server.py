#!/usr/bin/env python3
"""
Start MongoDB Server
Simple script to start the MongoDB production server
"""

import uvicorn
from main_mongodb_fixed import app

if __name__ == "__main__":
    print("🚀 Starting MongoDB Production Server...")
    print("📍 Server will be available at: http://localhost:8002")
    print("📚 API Documentation: http://localhost:8002/docs")
    print("🏥 Health Check: http://localhost:8002/health")
    print("=" * 60)
    
    uvicorn.run(
        app,
        host="0.0.0.0",
        port=8002,
        reload=False,
        log_level="info"
    ) 