"""
Promotions & Referrals Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime, timedelta
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid
import random
import string

class PromotionsReferralsService:
    """Service for promotions and referrals operations"""
    
    @staticmethod
    def generate_referral_code(length: int = 8) -> str:
        """Generate unique referral code"""
        characters = string.ascii_uppercase + string.digits
        return ''.join(random.choice(characters) for _ in range(length))
    
    @staticmethod
    async def get_user_referral_code(user_id: str):
        """Get or create user's referral code"""
        db = await get_database()
        
        referral = await db.user_referrals.find_one({"user_id": user_id})
        if not referral:
            referral_code = PromotionsReferralsService.generate_referral_code()
            
            referral = {
                "_id": str(uuid.uuid4()),
                "user_id": user_id,
                "referral_code": referral_code,
                "total_referrals": 0,
                "successful_referrals": 0,
                "total_earnings": 0,
                "created_at": datetime.utcnow()
            }
            await db.user_referrals.insert_one(referral)
        
        return referral
    
    @staticmethod
    async def process_referral(referrer_code: str, new_user_id: str):
        """Process a new referral"""
        db = await get_database()
        
        # Find referrer
        referrer = await db.user_referrals.find_one({"referral_code": referrer_code})
        if not referrer:
            return {"success": False, "error": "Invalid referral code"}
        
        # Check if user already referred
        existing = await db.referral_records.find_one({
            "referred_user_id": new_user_id
        })
        if existing:
            return {"success": False, "error": "User already referred"}
        
        # Create referral record
        referral_record = {
            "_id": str(uuid.uuid4()),
            "referrer_user_id": referrer["user_id"],
            "referred_user_id": new_user_id,
            "referral_code": referrer_code,
            "status": "pending",
            "reward_amount": 10.0,  # $10 reward
            "created_at": datetime.utcnow()
        }
        await db.referral_records.insert_one(referral_record)
        
        # Update referrer stats
        await db.user_referrals.update_one(
            {"_id": referrer["_id"]},
            {"$inc": {"total_referrals": 1}}
        )
        
        return {"success": True, "reward_amount": 10.0}
    
    @staticmethod
    async def get_active_promotions(user_id: str = None):
        """Get active promotions"""
        db = await get_database()
        
        now = datetime.utcnow()
        
        promotions = await db.promotions.find({
            "status": "active",
            "start_date": {"$lte": now},
            "end_date": {"$gte": now}
        }).to_list(length=None)
        
        return promotions
    
    @staticmethod
    async def create_promotion(user_id: str, promotion_data: Dict[str, Any]):
        """Create new promotion"""
        db = await get_database()
        
        promotion = {
            "_id": str(uuid.uuid4()),
            "created_by": user_id,
            "title": promotion_data.get("title"),
            "description": promotion_data.get("description"),
            "type": promotion_data.get("type", "discount"),  # discount, coupon, free_trial
            "value": promotion_data.get("value"),  # percentage or amount
            "code": promotion_data.get("code"),
            "usage_limit": promotion_data.get("usage_limit"),
            "used_count": 0,
            "start_date": datetime.fromisoformat(promotion_data.get("start_date")),
            "end_date": datetime.fromisoformat(promotion_data.get("end_date")),
            "status": "active",
            "created_at": datetime.utcnow()
        }
        
        result = await db.promotions.insert_one(promotion)
        return promotion
    
    @staticmethod
    async def get_referral_stats(user_id: str):
        """Get user's referral statistics"""
        db = await get_database()
        
        referral_info = await PromotionsReferralsService.get_user_referral_code(user_id)
        
        # Get detailed referral records
        referral_records = await db.referral_records.find({
            "referrer_user_id": user_id
        }).sort("created_at", -1).to_list(length=None)
        
        stats = {
            "referral_code": referral_info["referral_code"],
            "total_referrals": len(referral_records),
            "successful_referrals": len([r for r in referral_records if r["status"] == "completed"]),
            "pending_referrals": len([r for r in referral_records if r["status"] == "pending"]),
            "total_earnings": sum(r.get("reward_amount", 0) for r in referral_records if r["status"] == "completed"),
            "recent_referrals": referral_records[:10]
        }
        
        return stats