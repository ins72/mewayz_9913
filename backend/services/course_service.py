"""
Course Management Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid

class CourseService:
    """Service for course management operations"""
    
    @staticmethod
    async def get_courses(user_id: str):
        """Get user's courses"""
        db = await get_database()
        
        courses = await db.courses.find({"instructor_id": user_id}).to_list(length=None)
        return courses
    
    @staticmethod
    async def create_course(user_id: str, course_data: Dict[str, Any]):
        """Create new course"""
        db = await get_database()
        
        course = {
            "_id": str(uuid.uuid4()),
            "instructor_id": user_id,
            "title": course_data.get("title"),
            "description": course_data.get("description"),
            "category": course_data.get("category"),
            "price": course_data.get("price", 0),
            "duration": course_data.get("duration"),
            "difficulty": course_data.get("difficulty", "beginner"),
            "modules": course_data.get("modules", []),
            "status": "draft",
            "enrollment_count": 0,
            "rating": 0,
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        
        result = await db.courses.insert_one(course)
        return course
    
    @staticmethod
    async def get_enrollments(user_id: str):
        """Get course enrollments for instructor"""
        db = await get_database()
        
        enrollments = await db.course_enrollments.find({
            "instructor_id": user_id
        }).sort("enrolled_at", -1).to_list(length=None)
        
        return enrollments
    
    @staticmethod
    async def get_student_progress(course_id: str, student_id: str):
        """Get student progress in a course"""
        db = await get_database()
        
        progress = await db.course_progress.find_one({
            "course_id": course_id,
            "student_id": student_id
        })
        
        if not progress:
            progress = {
                "_id": str(uuid.uuid4()),
                "course_id": course_id,
                "student_id": student_id,
                "completed_modules": [],
                "progress_percentage": 0,
                "started_at": datetime.utcnow()
            }
            await db.course_progress.insert_one(progress)
        
        return progress