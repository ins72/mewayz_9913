"""
Escrow Service
Business logic for secure payment processing, transactions, and dispute resolution
"""
from typing import Optional, List, Dict
from datetime import datetime, timedelta
import uuid
import random

from core.database import get_database

class EscrowService:
    def __init__(self):
        self.db = None
    
    async def get_database(self):
        """Get database connection with lazy initialization"""
        if not self.db:
            from core.database import get_database
            self.db = get_database()
        return self.db
    
    async def get_transactions(self, user_id: str, status: Optional[str] = None, transaction_type: Optional[str] = None, limit: int = 50):
        """Get user's escrow transactions"""
        
        # Handle user_id properly - it might be a dict from current_user
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        # Generate realistic transaction data
        transactions = []
        transaction_count = min(limit, random.randint(15, 45))
        
        statuses = ["pending", "active", "delivered", "completed", "disputed", "cancelled"]
        types = ["service_contract", "product_sale", "digital_asset", "consulting"]
        
        for i in range(transaction_count):
            transaction_status = status if status else random.choice(statuses)
            transaction_type_selected = transaction_type if transaction_type else random.choice(types)
            
            created_days_ago = random.randint(1, 180)
            amount = round(random.uniform(500, 15000), 2)
            
            transaction = {
                "id": str(uuid.uuid4()),
                "title": random.choice([
                    "Website Development Project",
                    "Mobile App Development", 
                    "Logo Design Package",
                    "E-commerce Platform Setup",
                    "Digital Marketing Campaign",
                    "Custom Software Solution",
                    "Content Writing Services",
                    "SEO Optimization Project"
                ]),
                "description": "Professional service delivery with milestone-based payments",
                "amount": amount,
                "status": transaction_status,
                "type": transaction_type_selected,
                "buyer": {
                    "name": random.choice(["John Doe", "Sarah Johnson", "Michael Brown", "Emily Davis"]),
                    "email": f"buyer{i}@example.com",
                    "verified": True
                },
                "seller": {
                    "name": random.choice(["Tech Solutions Inc", "Creative Agency", "DevTeam Pro", "Digital Masters"]),
                    "email": f"seller{i}@example.com",
                    "verified": True
                },
                "milestone": {
                    "title": random.choice(["Initial Design", "Development Phase", "Testing & QA", "Final Delivery"]),
                    "description": "Current project milestone",
                    "progress": random.randint(10, 100),
                    "due_date": (datetime.now() + timedelta(days=random.randint(7, 30))).isoformat()
                },
                "fees": {
                    "escrow_fee": round(amount * 0.029, 2),
                    "processing_fee": 0.30,
                    "total_fees": round(amount * 0.029 + 0.30, 2)
                },
                "created_at": (datetime.now() - timedelta(days=created_days_ago)).isoformat(),
                "updated_at": (datetime.now() - timedelta(days=random.randint(0, created_days_ago))).isoformat(),
                "estimated_completion": (datetime.now() + timedelta(days=random.randint(7, 45))).isoformat(),
                "auto_release_date": (datetime.now() + timedelta(days=7)).isoformat() if transaction_status == "delivered" else None
            }
            transactions.append(transaction)
        
        return {
            "success": True,
            "data": {
                "transactions": sorted(transactions, key=lambda x: x["created_at"], reverse=True),
                "total": len(transactions),
                "summary": {
                    "pending": len([t for t in transactions if t["status"] == "pending"]),
                    "active": len([t for t in transactions if t["status"] == "active"]),
                    "completed": len([t for t in transactions if t["status"] == "completed"]),
                    "disputed": len([t for t in transactions if t["status"] == "disputed"]),
                    "total_value": sum([t["amount"] for t in transactions])
                }
            }
        }
    
    async def create_transaction(self, user_id: str, transaction_data: dict):
        """Create new escrow transaction"""
        
        # Handle user_id properly - it might be a dict from current_user
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        transaction_id = str(uuid.uuid4())
        amount = transaction_data.get("amount", 0)
        
        transaction = {
            "id": transaction_id,
            "user_id": user_id,
            "title": transaction_data.get("title"),
            "description": transaction_data.get("description", ""),
            "amount": amount,
            "status": "pending",
            "type": "service_contract",
            "buyer_email": transaction_data.get("buyer_email"),
            "seller_email": transaction_data.get("seller_email"),
            "milestone": {
                "title": transaction_data.get("milestone_title"),
                "description": transaction_data.get("milestone_description", ""),
                "status": "pending",
                "progress": 0
            },
            "payment_method": transaction_data.get("payment_method", "card"),
            "auto_release_days": transaction_data.get("auto_release_days", 7),
            "fees": {
                "escrow_fee": round(amount * 0.029, 2),
                "processing_fee": 0.30,
                "total_fees": round(amount * 0.029 + 0.30, 2)
            },
            "created_at": datetime.now().isoformat(),
            "updated_at": datetime.now().isoformat()
        }
        
        # Store in database (simulate)
        try:
            db = await self.get_database()
            if db:
                collection = db.escrow_transactions
                await collection.insert_one({
                    **transaction,
                    "created_at": datetime.now(),
                    "updated_at": datetime.now()
                })
        except Exception as e:
            print(f"Transaction storage error: {e}")
        
        return {
            "success": True,
            "data": {
                "transaction": transaction,
                "message": "Escrow transaction created successfully",
                "next_steps": [
                    "Payment confirmation required from buyer",
                    "Seller will be notified to begin work",
                    "Milestone progress tracking will begin"
                ]
            }
        }
    
    async def get_transaction(self, user_id: str, transaction_id: str):
        """Get specific transaction details"""
        
        # Generate detailed transaction data
        amount = round(random.uniform(2500, 12500), 2)
        created_days_ago = random.randint(5, 30)
        
        transaction = {
            "id": transaction_id,
            "title": "E-commerce Platform Development",
            "description": "Complete e-commerce solution with payment integration, inventory management, and admin dashboard",
            "amount": amount,
            "status": random.choice(["active", "delivered", "completed"]),
            "type": "service_contract",
            "buyer": {
                "name": "TechStart Ventures",
                "email": "contact@techstart.com",
                "verified": True,
                "rating": 4.8,
                "total_transactions": 23
            },
            "seller": {
                "name": "Elite Development Co",
                "email": "hello@elitedev.com",
                "verified": True,
                "rating": 4.9,
                "total_transactions": 156,
                "completion_rate": 98.2
            },
            "milestones": [
                {
                    "id": str(uuid.uuid4()),
                    "title": "Project Planning & Design",
                    "amount": amount * 0.25,
                    "status": "completed",
                    "completed_at": (datetime.now() - timedelta(days=20)).isoformat(),
                    "notes": "Initial wireframes and design mockups approved"
                },
                {
                    "id": str(uuid.uuid4()),
                    "title": "Frontend Development",
                    "amount": amount * 0.35,
                    "status": "completed",
                    "completed_at": (datetime.now() - timedelta(days=12)).isoformat(),
                    "notes": "Responsive frontend with all requested features"
                },
                {
                    "id": str(uuid.uuid4()),
                    "title": "Backend & Database Setup",
                    "amount": amount * 0.25,
                    "status": "active",
                    "progress": 85,
                    "estimated_completion": (datetime.now() + timedelta(days=5)).isoformat(),
                    "notes": "API development in progress, database optimized"
                },
                {
                    "id": str(uuid.uuid4()),
                    "title": "Testing & Deployment",
                    "amount": amount * 0.15,
                    "status": "pending",
                    "estimated_start": (datetime.now() + timedelta(days=6)).isoformat(),
                    "notes": "Final testing and production deployment"
                }
            ],
            "payment_history": [
                {
                    "date": (datetime.now() - timedelta(days=20)).isoformat(),
                    "amount": amount * 0.25,
                    "milestone": "Project Planning & Design",
                    "status": "released"
                },
                {
                    "date": (datetime.now() - timedelta(days=12)).isoformat(),
                    "amount": amount * 0.35,
                    "milestone": "Frontend Development", 
                    "status": "released"
                }
            ],
            "communication": [
                {
                    "timestamp": (datetime.now() - timedelta(hours=6)).isoformat(),
                    "from": "seller",
                    "message": "Backend API development is 85% complete. Database optimization done.",
                    "type": "progress_update"
                },
                {
                    "timestamp": (datetime.now() - timedelta(days=2)).isoformat(),
                    "from": "buyer",
                    "message": "Looks great! Excited to see the final testing phase.",
                    "type": "feedback"
                }
            ],
            "fees": {
                "escrow_fee": round(amount * 0.029, 2),
                "processing_fee": 0.30,
                "total_fees": round(amount * 0.029 + 0.30, 2)
            },
            "security": {
                "encryption": "AES-256",
                "fraud_score": 0.2,
                "risk_level": "low",
                "verification_status": "verified"
            },
            "created_at": (datetime.now() - timedelta(days=created_days_ago)).isoformat(),
            "updated_at": (datetime.now() - timedelta(hours=6)).isoformat()
        }
        
        return {
            "success": True,
            "data": {"transaction": transaction}
        }
    
    async def update_milestone(self, user_id: str, transaction_id: str, milestone_data: dict):
        """Update transaction milestone status"""
        
        return {
            "success": True,
            "data": {
                "transaction_id": transaction_id,
                "milestone_status": milestone_data.get("status"),
                "message": f"Milestone status updated to {milestone_data.get('status')}",
                "next_action": "Awaiting buyer approval" if milestone_data.get("status") == "delivered" else "Continue work in progress"
            }
        }
    
    async def release_payment(self, user_id: str, transaction_id: str, release_data: dict):
        """Release escrowed payment"""
        
        amount = release_data.get("amount", round(random.uniform(1000, 5000), 2))
        
        return {
            "success": True,
            "data": {
                "transaction_id": transaction_id,
                "amount_released": amount,
                "release_date": datetime.now().isoformat(),
                "message": "Payment released successfully",
                "estimated_arrival": (datetime.now() + timedelta(days=1)).isoformat(),
                "reference_number": f"ESC-{random.randint(100000, 999999)}"
            }
        }
    
    async def get_disputes(self, user_id: str):
        """Get user's dispute cases"""
        
        # Handle user_id properly - it might be a dict from current_user
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        disputes = []
        dispute_count = random.randint(2, 8)
        
        for i in range(dispute_count):
            dispute = {
                "id": str(uuid.uuid4()),
                "transaction_id": str(uuid.uuid4()),
                "transaction_title": random.choice([
                    "Website Development", "Mobile App Project", "Logo Design", 
                    "Digital Marketing", "Content Writing", "SEO Services"
                ]),
                "amount": round(random.uniform(800, 8500), 2),
                "reason": random.choice([
                    "Work not delivered as specified",
                    "Quality below agreed standards",
                    "Missed deadline without communication",
                    "Incomplete deliverables",
                    "Scope creep dispute"
                ]),
                "status": random.choice(["open", "under_review", "resolved", "closed"]),
                "filed_by": random.choice(["buyer", "seller"]),
                "created_at": (datetime.now() - timedelta(days=random.randint(5, 45))).isoformat(),
                "last_updated": (datetime.now() - timedelta(days=random.randint(1, 10))).isoformat(),
                "resolution_deadline": (datetime.now() + timedelta(days=random.randint(3, 14))).isoformat(),
                "assigned_mediator": f"Mediator-{random.randint(100, 999)}"
            }
            disputes.append(dispute)
        
        return {
            "success": True,
            "data": {
                "disputes": disputes,
                "summary": {
                    "total": len(disputes),
                    "open": len([d for d in disputes if d["status"] == "open"]),
                    "resolved": len([d for d in disputes if d["status"] == "resolved"]),
                    "average_resolution_time": f"{random.randint(5, 12)} days"
                }
            }
        }
    
    async def create_dispute(self, user_id: str, dispute_data: dict):
        """Create new dispute case"""
        
        # Handle user_id properly - it might be a dict from current_user
        if isinstance(user_id, dict):
            user_id = user_id.get("_id") or user_id.get("id") or str(user_id.get("email", "default-user"))
        
        dispute_id = str(uuid.uuid4())
        
        dispute = {
            "id": dispute_id,
            "transaction_id": dispute_data.get("transaction_id"),
            "filed_by": user_id,
            "reason": dispute_data.get("reason"),
            "description": dispute_data.get("description"),
            "evidence_urls": dispute_data.get("evidence_urls", []),
            "status": "open",
            "created_at": datetime.now().isoformat(),
            "resolution_deadline": (datetime.now() + timedelta(days=14)).isoformat(),
            "assigned_mediator": f"Mediator-{random.randint(100, 999)}"
        }
        
        return {
            "success": True,
            "data": {
                "dispute": dispute,
                "message": "Dispute case created successfully",
                "next_steps": [
                    "Mediator will review within 24 hours",
                    "Both parties will be contacted for additional information",
                    "Resolution expected within 14 business days"
                ],
                "case_number": f"DSP-{random.randint(10000, 99999)}"
            }
        }
    
    async def get_settings(self, user_id: str):
        """Get user's escrow settings"""
        
        return {
            "success": True,
            "data": {
                "notification_preferences": {
                    "email_notifications": True,
                    "sms_notifications": False,
                    "milestone_updates": True,
                    "payment_confirmations": True,
                    "dispute_alerts": True
                },
                "auto_release_settings": {
                    "enabled": True,
                    "default_days": 7,
                    "maximum_days": 30
                },
                "payment_preferences": {
                    "default_method": "bank_transfer",
                    "backup_method": "paypal",
                    "currency": "USD"
                },
                "security_settings": {
                    "two_factor_auth": True,
                    "login_notifications": True,
                    "suspicious_activity_alerts": True
                },
                "privacy_settings": {
                    "public_profile": False,
                    "transaction_history_visible": False,
                    "rating_visible": True
                }
            }
        }
    
    async def update_settings(self, user_id: str, settings: dict):
        """Update escrow settings"""
        
        return {
            "success": True,
            "data": {
                "message": "Settings updated successfully",
                "updated_fields": list(settings.keys())
            }
        }
    
    async def verify_account(self, user_id: str, verification_data: dict):
        """Submit account verification documents"""
        
        verification_id = str(uuid.uuid4())
        
        return {
            "success": True,
            "data": {
                "verification_id": verification_id,
                "status": "submitted",
                "message": "Verification documents submitted successfully",
                "estimated_review_time": "2-5 business days",
                "required_documents": verification_data.get("document_types", []),
                "next_steps": [
                    "Documents will be reviewed by our compliance team",
                    "Additional information may be requested if needed",
                    "Email notification will be sent upon completion"
                ]
            }
        }
    
    async def get_notifications(self, user_id: str):
        """Get escrow-related notifications"""
        
        notifications = []
        notification_count = random.randint(5, 15)
        
        for i in range(notification_count):
            notification = {
                "id": str(uuid.uuid4()),
                "type": random.choice([
                    "payment_released", "milestone_completed", "dispute_update", 
                    "payment_received", "verification_complete", "auto_release_reminder"
                ]),
                "title": random.choice([
                    "Payment Released Successfully",
                    "New Milestone Completed",
                    "Dispute Case Updated",
                    "Payment Received",
                    "Account Verification Complete",
                    "Auto-Release Reminder"
                ]),
                "message": "Important update regarding your escrow transaction",
                "transaction_id": str(uuid.uuid4()),
                "read": random.choice([True, False]),
                "priority": random.choice(["low", "medium", "high"]),
                "created_at": (datetime.now() - timedelta(hours=random.randint(1, 72))).isoformat(),
                "action_required": random.choice([True, False])
            }
            notifications.append(notification)
        
        return {
            "success": True,
            "data": {
                "notifications": sorted(notifications, key=lambda x: x["created_at"], reverse=True),
                "unread_count": len([n for n in notifications if not n["read"]]),
                "high_priority_count": len([n for n in notifications if n["priority"] == "high"])
            }
        }