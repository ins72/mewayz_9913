"""
Form Builder Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid

class FormBuilderService:
    """Service for form builder operations"""
    
    @staticmethod
    async def get_forms(user_id: str):
        """Get user's forms"""
        db = await get_database()
        
        forms = await db.forms.find({"user_id": user_id}).sort("created_at", -1).to_list(length=None)
        return forms
    
    @staticmethod
    async def create_form(user_id: str, form_data: Dict[str, Any]):
        """Create new form"""
        db = await get_database()
        
        form = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "title": form_data.get("title"),
            "description": form_data.get("description", ""),
            "fields": form_data.get("fields", []),
            "settings": {
                "allow_multiple_submissions": form_data.get("allow_multiple", True),
                "require_login": form_data.get("require_login", False),
                "success_message": form_data.get("success_message", "Thank you for your submission!"),
                "redirect_url": form_data.get("redirect_url")
            },
            "status": "active",
            "submission_count": 0,
            "created_at": datetime.utcnow(),
            "updated_at": datetime.utcnow()
        }
        
        result = await db.forms.insert_one(form)
        return form
    
    @staticmethod
    async def get_form_submissions(user_id: str, form_id: str):
        """Get form submissions"""
        db = await get_database()
        
        # Verify form ownership
        form = await db.forms.find_one({"_id": form_id, "user_id": user_id})
        if not form:
            return []
        
        submissions = await db.form_submissions.find({
            "form_id": form_id
        }).sort("submitted_at", -1).to_list(length=None)
        
        return submissions
    
    @staticmethod
    async def submit_form(form_id: str, submission_data: Dict[str, Any], submitter_ip: str = None):
        """Submit form data"""
        db = await get_database()
        
        # Verify form exists and is active
        form = await db.forms.find_one({"_id": form_id, "status": "active"})
        if not form:
            return None
        
        submission = {
            "_id": str(uuid.uuid4()),
            "form_id": form_id,
            "data": submission_data,
            "submitter_ip": submitter_ip,
            "submitted_at": datetime.utcnow()
        }
        
        result = await db.form_submissions.insert_one(submission)
        
        # Update submission count
        await db.forms.update_one(
            {"_id": form_id},
            {"$inc": {"submission_count": 1}}
        )
        
        return submission

# Global service instance
form_builder_service = FormBuilderService()
