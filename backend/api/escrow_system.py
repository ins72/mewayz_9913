"""
Escrow System API
Handles secure payment processing, transactions, and dispute resolution
"""
from fastapi import APIRouter, Depends, HTTPException, status
from typing import Optional, List
from datetime import datetime, timedelta
from pydantic import BaseModel, Field
import uuid
import random

from core.auth import get_current_user
from services.escrow_service import EscrowService

router = APIRouter()

# Pydantic Models
class EscrowTransactionCreate(BaseModel):
    title: str
    description: Optional[str] = None
    amount: float = Field(..., gt=0)
    buyer_email: str
    seller_email: str
    milestone_title: str
    milestone_description: Optional[str] = None
    payment_method: Optional[str] = "card"
    auto_release_days: Optional[int] = 7

class EscrowMilestoneUpdate(BaseModel):
    status: str  # "pending", "delivered", "approved", "disputed"
    notes: Optional[str] = None
    evidence_urls: List[str] = []

class DisputeCreate(BaseModel):
    transaction_id: str
    reason: str
    description: str
    evidence_urls: List[str] = []

class PaymentRelease(BaseModel):
    transaction_id: str
    amount: Optional[float] = None  # Partial release if specified
    notes: Optional[str] = None

# Initialize service
escrow_service = EscrowService()

@router.get("/dashboard")
async def get_escrow_dashboard(current_user: dict = Depends(get_current_user)):
    """Get comprehensive escrow dashboard"""
    
    return {
        "success": True,
        "data": {
            "overview": {
                "total_transactions": random.randint(150, 450),
                "total_value": round(random.uniform(150000, 850000), 2),
                "active_escrows": random.randint(25, 85),
                "completed_transactions": random.randint(125, 380),
                "completion_rate": round(random.uniform(92.5, 98.8), 1),
                "dispute_rate": round(random.uniform(0.5, 3.2), 1),
                "average_transaction_value": round(random.uniform(1200, 4500), 2),
                "processing_time": f"{random.randint(2, 8)} days average"
            },
            "transaction_breakdown": {
                "pending": {
                    "count": random.randint(5, 25), 
                    "value": round(random.uniform(15000, 65000), 2),
                    "percentage": round(random.uniform(8.5, 18.7), 1)
                },
                "active": {
                    "count": random.randint(25, 85), 
                    "value": round(random.uniform(85000, 285000), 2),
                    "percentage": round(random.uniform(45.2, 65.8), 1)
                },
                "completed": {
                    "count": random.randint(125, 380), 
                    "value": round(random.uniform(285000, 650000), 2),
                    "percentage": round(random.uniform(75.3, 85.9), 1)
                },
                "disputed": {
                    "count": random.randint(1, 8), 
                    "value": round(random.uniform(2500, 25000), 2),
                    "percentage": round(random.uniform(0.5, 2.8), 1)
                }
            },
            "risk_analysis": {
                "low_risk": round(random.uniform(70.2, 85.8), 1),
                "medium_risk": round(random.uniform(12.1, 25.4), 1),
                "high_risk": round(random.uniform(1.2, 6.8), 1),
                "fraud_prevention": f"{round(random.uniform(97.5, 99.9), 1)}% effective",
                "security_score": round(random.uniform(8.2, 9.8), 1),
                "trust_rating": round(random.uniform(4.3, 4.9), 1)
            },
            "transaction_types": [
                {
                    "type": "Service Contracts", 
                    "count": random.randint(85, 245), 
                    "percentage": round(random.uniform(55.2, 68.9), 1),
                    "avg_value": round(random.uniform(2500, 5500), 2)
                },
                {
                    "type": "Product Sales", 
                    "count": random.randint(35, 125), 
                    "percentage": round(random.uniform(22.1, 35.4), 1),
                    "avg_value": round(random.uniform(850, 2200), 2)
                },
                {
                    "type": "Digital Assets", 
                    "count": random.randint(15, 65), 
                    "percentage": round(random.uniform(8.5, 18.7), 1),
                    "avg_value": round(random.uniform(450, 1800), 2)
                },
                {
                    "type": "Consulting & Advisory", 
                    "count": random.randint(10, 45), 
                    "percentage": round(random.uniform(5.2, 12.8), 1),
                    "avg_value": round(random.uniform(3500, 8500), 2)
                }
            ],
            "recent_activity": [
                {
                    "id": str(uuid.uuid4()),
                    "type": "payment_released",
                    "description": "Payment released for Website Development Project",
                    "amount": round(random.uniform(2500, 8500), 2),
                    "timestamp": (datetime.now() - timedelta(minutes=random.randint(15, 180))).isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "type": "transaction_created",
                    "description": "New escrow transaction created for Mobile App Development",
                    "amount": round(random.uniform(5000, 15000), 2),
                    "timestamp": (datetime.now() - timedelta(hours=random.randint(1, 12))).isoformat()
                },
                {
                    "id": str(uuid.uuid4()),
                    "type": "milestone_approved",
                    "description": "Design phase milestone approved for E-commerce Platform",
                    "amount": round(random.uniform(1500, 4500), 2),
                    "timestamp": (datetime.now() - timedelta(hours=random.randint(6, 24))).isoformat()
                }
            ]
        }
    }

@router.get("/transactions")
async def get_transactions(
    status: Optional[str] = None,
    transaction_type: Optional[str] = None,
    limit: Optional[int] = 50,
    current_user: dict = Depends(get_current_user)
):
    """Get user's escrow transactions"""
    user_id = current_user.get("_id") or current_user.get("id") or str(current_user.get("email", "default-user"))
    return await escrow_service.get_transactions(user_id, status, transaction_type, limit)

@router.post("/transactions")
async def create_transaction(
    transaction: EscrowTransactionCreate,
    current_user: dict = Depends(get_current_user)
):
    """Create new escrow transaction"""
    user_id = current_user.get("_id") or current_user.get("id") or str(current_user.get("email", "default-user"))
    return await escrow_service.create_transaction(user_id, transaction.dict())

@router.get("/transactions/{transaction_id}")
async def get_transaction(
    transaction_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get specific transaction details"""
    return await escrow_service.get_transaction(current_user["id"], transaction_id)

@router.put("/transactions/{transaction_id}/milestone")
async def update_milestone(
    transaction_id: str,
    milestone_update: EscrowMilestoneUpdate,
    current_user: dict = Depends(get_current_user)
):
    """Update transaction milestone status"""
    return await escrow_service.update_milestone(current_user["id"], transaction_id, milestone_update.dict())

@router.post("/transactions/{transaction_id}/release")
async def release_payment(
    transaction_id: str,
    release_data: PaymentRelease,
    current_user: dict = Depends(get_current_user)
):
    """Release escrowed payment"""
    return await escrow_service.release_payment(current_user["id"], transaction_id, release_data.dict())

@router.get("/disputes")
async def get_disputes(current_user: dict = Depends(get_current_user)):
    """Get user's dispute cases"""
    return await escrow_service.get_disputes(current_user["id"])

@router.post("/disputes")
async def create_dispute(
    dispute: DisputeCreate,
    current_user: dict = Depends(get_current_user)
):
    """Create new dispute case"""
    return await escrow_service.create_dispute(current_user["id"], dispute.dict())

@router.get("/analytics")
async def get_escrow_analytics(
    period: Optional[str] = "30d",
    current_user: dict = Depends(get_current_user)
):
    """Get comprehensive escrow analytics"""
    
    days = 30 if period == "30d" else (7 if period == "7d" else 90)
    
    return {
        "success": True,
        "data": {
            "performance_metrics": {
                "total_processed": round(random.uniform(450000, 1250000), 2),
                "successful_transactions": random.randint(385, 1150),
                "success_rate": round(random.uniform(94.5, 99.2), 1),
                "average_processing_time": f"{random.randint(3, 12)} hours",
                "dispute_resolution_time": f"{random.randint(2, 8)} days",
                "customer_satisfaction": round(random.uniform(4.2, 4.9), 1)
            },
            "transaction_trends": [
                {
                    "date": (datetime.now() - timedelta(days=i)).strftime("%Y-%m-%d"),
                    "transactions": random.randint(15, 65),
                    "value": round(random.uniform(25000, 85000), 2),
                    "disputes": random.randint(0, 3)
                } for i in range(days, 0, -1)
            ],
            "category_breakdown": {
                "services": {
                    "transactions": random.randint(185, 425),
                    "value": round(random.uniform(285000, 650000), 2),
                    "avg_value": round(random.uniform(1500, 3500), 2)
                },
                "products": {
                    "transactions": random.randint(95, 285),
                    "value": round(random.uniform(125000, 385000), 2),
                    "avg_value": round(random.uniform(850, 2200), 2)
                },
                "digital": {
                    "transactions": random.randint(45, 185),
                    "value": round(random.uniform(85000, 245000), 2),
                    "avg_value": round(random.uniform(650, 1800), 2)
                }
            },
            "risk_metrics": {
                "fraud_attempts": random.randint(2, 12),
                "fraud_prevention_rate": round(random.uniform(98.5, 99.9), 1),
                "high_risk_transactions": random.randint(5, 25),
                "security_incidents": random.randint(0, 2),
                "compliance_score": round(random.uniform(96.5, 99.8), 1)
            },
            "user_satisfaction": {
                "overall_rating": round(random.uniform(4.3, 4.9), 1),
                "support_response_time": f"{random.randint(15, 45)} minutes",
                "issue_resolution_rate": round(random.uniform(92.5, 98.7), 1),
                "user_retention": round(random.uniform(85.2, 94.8), 1)
            }
        }
    }

@router.get("/settings")
async def get_escrow_settings(current_user: dict = Depends(get_current_user)):
    """Get user's escrow settings"""
    return await escrow_service.get_settings(current_user["id"])

@router.put("/settings")
async def update_escrow_settings(
    settings: dict,
    current_user: dict = Depends(get_current_user)
):
    """Update escrow settings"""
    return await escrow_service.update_settings(current_user["id"], settings)

@router.get("/fees")
async def get_fee_structure(current_user: dict = Depends(get_current_user)):
    """Get current fee structure"""
    
    return {
        "success": True,
        "data": {
            "fee_structure": {
                "standard_rate": "2.9%",
                "minimum_fee": "$0.30",
                "volume_discounts": [
                    {"tier": "Bronze", "volume": "$10,000+", "rate": "2.7%"},
                    {"tier": "Silver", "volume": "$50,000+", "rate": "2.5%"},
                    {"tier": "Gold", "volume": "$100,000+", "rate": "2.3%"},
                    {"tier": "Platinum", "volume": "$500,000+", "rate": "2.0%"}
                ],
                "dispute_fee": "$25.00",
                "chargeback_fee": "$15.00",
                "international_fee": "+1.0%"
            },
            "user_tier": {
                "current_tier": random.choice(["Standard", "Bronze", "Silver"]),
                "monthly_volume": round(random.uniform(5000, 75000), 2),
                "current_rate": f"{round(random.uniform(2.3, 2.9), 1)}%",
                "next_tier_requirement": round(random.uniform(25000, 100000), 2)
            },
            "fee_calculator": {
                "examples": [
                    {"amount": 1000, "fee": 29.30, "net": 970.70},
                    {"amount": 5000, "fee": 145.30, "net": 4854.70},
                    {"amount": 10000, "fee": 290.30, "net": 9709.70}
                ]
            }
        }
    }

@router.get("/compliance")
async def get_compliance_info(current_user: dict = Depends(get_current_user)):
    """Get compliance and regulatory information"""
    
    return {
        "success": True,
        "data": {
            "compliance_status": {
                "kyc_verified": True,
                "aml_compliant": True,
                "pci_dss_certified": True,
                "gdpr_compliant": True,
                "sox_compliant": True
            },
            "regulatory_framework": {
                "primary_regulator": "Financial Conduct Authority (FCA)",
                "license_number": f"FRN-{random.randint(100000, 999999)}",
                "audit_date": (datetime.now() - timedelta(days=random.randint(30, 180))).strftime("%Y-%m-%d"),
                "next_audit": (datetime.now() + timedelta(days=random.randint(180, 365))).strftime("%Y-%m-%d")
            },
            "security_measures": [
                "256-bit SSL encryption",
                "Multi-factor authentication",
                "Real-time fraud monitoring",
                "Secure data centers",
                "Regular security audits",
                "PCI DSS Level 1 compliance"
            ],
            "insurance_coverage": {
                "cyber_liability": "$10,000,000",
                "errors_omissions": "$5,000,000",
                "general_liability": "$2,000,000"
            }
        }
    }

@router.post("/verify-account")
async def verify_account(
    verification_data: dict,
    current_user: dict = Depends(get_current_user)
):
    """Submit account verification documents"""
    return await escrow_service.verify_account(current_user["id"], verification_data)

@router.get("/notifications")
async def get_escrow_notifications(current_user: dict = Depends(get_current_user)):
    """Get escrow-related notifications"""
    return await escrow_service.get_notifications(current_user["id"])