@echo off
cd backend
call venv\Scripts\activate.bat
uvicorn main:app --host 0.0.0.0 --port 8001 --reload
pause
