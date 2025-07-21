"""
AI Token Management Services Business Logic
Professional Mewayz Platform
"""

from datetime import datetime, timedelta
from typing import Dict, Any, List, Optional
from core.database import get_database
import uuid

class AITokenService:
    """Service for AI token management operations"""
    
    @staticmethod
    async def get_user_token_balance(user_id: str):
        """Get user's AI token balance"""
        db = await get_database()
        
        user_tokens = await db.user_tokens.find_one({"user_id": user_id})
        if not user_tokens:
            # Initialize user tokens
            user_tokens = {
                "_id": str(uuid.uuid4()),
                "user_id": user_id,
                "balance": 1000,  # Free tier starts with 1000 tokens
                "total_earned": 1000,
                "total_spent": 0,
                "tier": "free",
                "created_at": datetime.utcnow(),
                "updated_at": datetime.utcnow()
            }
            await db.user_tokens.insert_one(user_tokens)
        
        return user_tokens
    
    @staticmethod
    async def deduct_tokens(user_id: str, amount: int, operation: str = "ai_request"):
        """Deduct tokens from user balance"""
        db = await get_database()
        
        # Get current balance
        user_tokens = await AITokenService.get_user_token_balance(user_id)
        
        if user_tokens["balance"] < amount:
            return {"success": False, "error": "Insufficient token balance"}
        
        # Deduct tokens
        new_balance = user_tokens["balance"] - amount
        await db.user_tokens.update_one(
            {"user_id": user_id},
            {
                "$set": {
                    "balance": new_balance,
                    "total_spent": user_tokens["total_spent"] + amount,
                    "updated_at": datetime.utcnow()
                }
            }
        )
        
        # Log transaction
        transaction = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "type": "deduct",
            "amount": amount,
            "operation": operation,
            "balance_after": new_balance,
            "created_at": datetime.utcnow()
        }
        await db.token_transactions.insert_one(transaction)
        
        return {"success": True, "new_balance": new_balance}
    
    @staticmethod
    async def add_tokens(user_id: str, amount: int, reason: str = "purchase"):
        """Add tokens to user balance"""
        db = await get_database()
        
        user_tokens = await AITokenService.get_user_token_balance(user_id)
        new_balance = user_tokens["balance"] + amount
        
        await db.user_tokens.update_one(
            {"user_id": user_id},
            {
                "$set": {
                    "balance": new_balance,
                    "total_earned": user_tokens["total_earned"] + amount,
                    "updated_at": datetime.utcnow()
                }
            }
        )
        
        # Log transaction
        transaction = {
            "_id": str(uuid.uuid4()),
            "user_id": user_id,
            "type": "add",
            "amount": amount,
            "operation": reason,
            "balance_after": new_balance,
            "created_at": datetime.utcnow()
        }
        await db.token_transactions.insert_one(transaction)
        
        return {"success": True, "new_balance": new_balance}
    
    @staticmethod
    async def get_token_usage_history(user_id: str, days: int = 30):
        """Get token usage history"""
        db = await get_database()
        
        since_date = datetime.utcnow() - timedelta(days=days)
        
        transactions = await db.token_transactions.find({
            "user_id": user_id,
            "created_at": {"$gte": since_date}
        }).sort("created_at", -1).to_list(length=None)
        
        return transactions

# Global service instance
ai_token_service = AITokenService()
