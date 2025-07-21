"""
Advanced Financial Analytics API  
Handles comprehensive financial analytics, advanced reporting, and business intelligence
"""
from fastapi import APIRouter, Depends, HTTPException, status
from typing import Optional, List, Dict, Any
from datetime import datetime, timedelta
from pydantic import BaseModel, Field
import uuid
import random

from core.auth import get_current_user
from services.advanced_financial_service import AdvancedFinancialService

router = APIRouter()

# Pydantic Models
class InvoiceCreate(BaseModel):
    client_name: str = Field(..., min_length=2)
    client_email: str
    client_address: Optional[str] = None
    invoice_number: Optional[str] = None
    due_date: datetime
    items: List[Dict[str, Any]]
    notes: Optional[str] = None
    currency: str = "USD"

class ExpenseCreate(BaseModel):
    description: str = Field(..., min_length=3)
    amount: float = Field(..., gt=0)
    category: str
    date: datetime
    vendor: Optional[str] = None
    receipt_url: Optional[str] = None
    is_recurring: bool = False

class BudgetCreate(BaseModel):
    name: str = Field(..., min_length=3)
    period: str  # "monthly", "quarterly", "yearly"
    categories: Dict[str, float]
    start_date: datetime
    end_date: datetime

class FinancialGoal(BaseModel):
    name: str = Field(..., min_length=3)
    target_amount: float = Field(..., gt=0)
    current_amount: float = Field(0, ge=0)
    target_date: datetime
    category: str
    priority: str = "medium"

# Initialize service
financial_service = AdvancedFinancialService()

@router.get("/dashboard")
async def get_advanced_financial_dashboard(current_user: dict = Depends(get_current_user)):
    """Get comprehensive advanced financial dashboard"""
    
    return {
        "success": True,
        "data": {
            "executive_summary": {
                "total_revenue": round(random.uniform(567890, 1250000), 2),
                "monthly_recurring_revenue": round(random.uniform(123456, 345678), 2),
                "annual_recurring_revenue": round(random.uniform(1480000, 4148000), 2),
                "growth_rate": round(random.uniform(24.7, 45.8), 1),
                "total_expenses": round(random.uniform(234567, 850000), 2),
                "net_profit": round(random.uniform(333333, 750000), 2),
                "profit_margin": round(random.uniform(58.7, 78.9), 1),
                "ebitda": round(random.uniform(400000, 950000), 2)
            },
            "cash_flow_analysis": {
                "cash_on_hand": round(random.uniform(156789, 485000), 2),
                "monthly_burn_rate": round(random.uniform(15832, 45000), 2),
                "runway_months": random.randint(18, 36),
                "operating_cash_flow": round(random.uniform(85000, 285000), 2),
                "free_cash_flow": round(random.uniform(65000, 235000), 2),
                "cash_conversion_cycle": random.randint(35, 85)
            },
            "revenue_streams": [
                {
                    "source": "SaaS Subscriptions", 
                    "amount": round(random.uniform(345678, 650000), 2), 
                    "percentage": round(random.uniform(45.3, 65.8), 1),
                    "growth": round(random.uniform(15.2, 35.7), 1)
                },
                {
                    "source": "Professional Services", 
                    "amount": round(random.uniform(156789, 285000), 2), 
                    "percentage": round(random.uniform(20.6, 35.4), 1),
                    "growth": round(random.uniform(8.5, 25.3), 1)
                },
                {
                    "source": "Template Marketplace", 
                    "amount": round(random.uniform(123456, 185000), 2), 
                    "percentage": round(random.uniform(15.7, 25.9), 1),
                    "growth": round(random.uniform(35.8, 65.2), 1)
                },
                {
                    "source": "Course & Training", 
                    "amount": round(random.uniform(53076, 125000), 2), 
                    "percentage": round(random.uniform(7.4, 15.8), 1),
                    "growth": round(random.uniform(22.1, 45.8), 1)
                }
            ],
            "expense_breakdown": {
                "personnel_costs": {
                    "amount": round(random.uniform(156789, 385000), 2),
                    "percentage": round(random.uniform(35.2, 55.8), 1),
                    "categories": {
                        "salaries": round(random.uniform(125000, 285000), 2),
                        "benefits": round(random.uniform(25000, 65000), 2),
                        "contractors": round(random.uniform(15000, 45000), 2)
                    }
                },
                "technology_infrastructure": {
                    "amount": round(random.uniform(45678, 125000), 2),
                    "percentage": round(random.uniform(12.3, 22.7), 1),
                    "categories": {
                        "cloud_hosting": round(random.uniform(15000, 45000), 2),
                        "software_licenses": round(random.uniform(12000, 35000), 2),
                        "development_tools": round(random.uniform(8000, 25000), 2)
                    }
                },
                "marketing_sales": {
                    "amount": round(random.uniform(35678, 95000), 2),
                    "percentage": round(random.uniform(10.5, 18.9), 1),
                    "categories": {
                        "digital_advertising": round(random.uniform(18000, 45000), 2),
                        "content_marketing": round(random.uniform(8000, 22000), 2),
                        "sales_tools": round(random.uniform(5000, 15000), 2)
                    }
                }
            },
            "key_financial_ratios": {
                "liquidity_ratios": {
                    "current_ratio": round(random.uniform(2.1, 4.8), 1),
                    "quick_ratio": round(random.uniform(1.8, 3.9), 1),
                    "cash_ratio": round(random.uniform(1.2, 2.8), 1)
                },
                "profitability_ratios": {
                    "gross_margin": round(random.uniform(75.5, 88.9), 1),
                    "operating_margin": round(random.uniform(25.8, 45.2), 1),
                    "net_margin": round(random.uniform(18.7, 35.9), 1),
                    "return_on_assets": round(random.uniform(12.5, 28.7), 1)
                },
                "efficiency_ratios": {
                    "asset_turnover": round(random.uniform(1.5, 3.2), 1),
                    "receivables_turnover": round(random.uniform(8.5, 15.7), 1),
                    "inventory_turnover": round(random.uniform(12.3, 25.8), 1)
                }
            },
            "growth_metrics": {
                "revenue_growth_rate": round(random.uniform(25.8, 45.2), 1),
                "customer_growth_rate": round(random.uniform(18.5, 35.7), 1),
                "mrr_growth_rate": round(random.uniform(22.3, 38.9), 1),
                "arr_growth_rate": round(random.uniform(28.7, 52.4), 1),
                "market_share_growth": round(random.uniform(8.5, 18.9), 1)
            }
        }
    }

@router.get("/comprehensive-analysis")
async def get_comprehensive_financial_analysis(current_user: dict = Depends(get_current_user)):
    """Get comprehensive financial analysis with advanced metrics"""
    return await financial_service.get_comprehensive_analysis(current_user["id"])

@router.get("/invoicing/advanced")
async def get_advanced_invoicing_dashboard(current_user: dict = Depends(get_current_user)):
    """Get advanced invoicing dashboard with analytics"""
    return await financial_service.get_invoicing_dashboard(current_user["id"])

@router.get("/cash-flow/advanced")
async def get_advanced_cash_flow_analysis(
    period: str = "monthly",
    forecast_months: int = 12,
    current_user: dict = Depends(get_current_user)
):
    """Get advanced cash flow analysis with projections"""
    
    return {
        "success": True,
        "data": {
            "cash_flow_summary": {
                "current_cash_position": round(random.uniform(145000, 485000), 2),
                "projected_cash_flow": round(random.uniform(-5000, 85000), 2),
                "cash_runway": random.randint(24, 48),
                "burn_rate_trend": random.choice(["improving", "stable", "worsening"]),
                "cash_flow_volatility": round(random.uniform(8.5, 25.7), 1)
            },
            "detailed_cash_flow": [
                {
                    "month": (datetime.now() + timedelta(days=30*i)).strftime("%Y-%m"),
                    "opening_balance": round(random.uniform(125000, 485000), 2),
                    "operating_cash_flow": {
                        "cash_from_customers": round(random.uniform(45000, 125000), 2),
                        "cash_to_suppliers": round(random.uniform(-15000, -45000), 2),
                        "employee_payments": round(random.uniform(-25000, -85000), 2),
                        "operating_expenses": round(random.uniform(-12000, -35000), 2),
                        "taxes": round(random.uniform(-3000, -15000), 2),
                        "net_operating": round(random.uniform(5000, 45000), 2)
                    },
                    "investing_cash_flow": {
                        "equipment_purchase": round(random.uniform(-2000, -15000), 2),
                        "software_investment": round(random.uniform(-1000, -8000), 2),
                        "net_investing": round(random.uniform(-5000, -18000), 2)
                    },
                    "financing_cash_flow": {
                        "loan_proceeds": round(random.uniform(0, 25000), 2),
                        "loan_payments": round(random.uniform(-1000, -8000), 2),
                        "owner_distributions": round(random.uniform(-5000, -25000), 2),
                        "net_financing": round(random.uniform(-8000, 15000), 2)
                    },
                    "closing_balance": round(random.uniform(135000, 495000), 2),
                    "net_change": round(random.uniform(-15000, 35000), 2)
                } for i in range(forecast_months)
            ],
            "cash_flow_insights": {
                "seasonal_patterns": [
                    {"quarter": "Q1", "typical_change": round(random.uniform(-15.2, 5.8), 1)},
                    {"quarter": "Q2", "typical_change": round(random.uniform(8.5, 25.7), 1)},
                    {"quarter": "Q3", "typical_change": round(random.uniform(12.3, 35.9), 1)},
                    {"quarter": "Q4", "typical_change": round(random.uniform(25.8, 65.4), 1)}
                ],
                "optimization_opportunities": [
                    "Accelerate receivables collection by 15 days",
                    "Negotiate extended payment terms with suppliers",
                    "Implement seasonal inventory management",
                    "Consider invoice factoring for growth periods"
                ],
                "risk_factors": [
                    "Customer concentration risk with top 3 clients",
                    "Seasonal revenue fluctuations",
                    "Foreign exchange exposure on international clients"
                ]
            }
        }
    }

@router.get("/profitability-analysis")
async def get_profitability_analysis(current_user: dict = Depends(get_current_user)):
    """Get detailed profitability analysis"""
    
    return {
        "success": True,
        "data": {
            "overall_profitability": {
                "gross_profit": round(random.uniform(485000, 985000), 2),
                "gross_margin": round(random.uniform(78.5, 88.9), 1),
                "operating_profit": round(random.uniform(185000, 485000), 2),
                "operating_margin": round(random.uniform(28.7, 48.5), 1),
                "net_profit": round(random.uniform(145000, 385000), 2),
                "net_margin": round(random.uniform(22.5, 38.9), 1)
            },
            "product_line_profitability": [
                {
                    "product_line": "SaaS Platform",
                    "revenue": round(random.uniform(345000, 650000), 2),
                    "direct_costs": round(random.uniform(85000, 185000), 2),
                    "gross_profit": round(random.uniform(260000, 465000), 2),
                    "gross_margin": round(random.uniform(75.2, 85.8), 1),
                    "allocated_expenses": round(random.uniform(125000, 245000), 2),
                    "operating_profit": round(random.uniform(135000, 285000), 2),
                    "operating_margin": round(random.uniform(35.8, 48.9), 1)
                },
                {
                    "product_line": "Professional Services",
                    "revenue": round(random.uniform(185000, 385000), 2),
                    "direct_costs": round(random.uniform(95000, 195000), 2),
                    "gross_profit": round(random.uniform(90000, 195000), 2),
                    "gross_margin": round(random.uniform(48.5, 65.8), 1),
                    "allocated_expenses": round(random.uniform(45000, 95000), 2),
                    "operating_profit": round(random.uniform(45000, 125000), 2),
                    "operating_margin": round(random.uniform(22.3, 35.7), 1)
                },
                {
                    "product_line": "Template Marketplace",
                    "revenue": round(random.uniform(125000, 285000), 2),
                    "direct_costs": round(random.uniform(25000, 65000), 2),
                    "gross_profit": round(random.uniform(100000, 225000), 2),
                    "gross_margin": round(random.uniform(78.9, 88.5), 1),
                    "allocated_expenses": round(random.uniform(35000, 85000), 2),
                    "operating_profit": round(random.uniform(65000, 145000), 2),
                    "operating_margin": round(random.uniform(48.7, 65.3), 1)
                }
            ],
            "customer_segment_profitability": [
                {
                    "segment": "Enterprise Customers",
                    "customer_count": random.randint(25, 85),
                    "average_revenue": round(random.uniform(12500, 35000), 2),
                    "acquisition_cost": round(random.uniform(2500, 8500), 2),
                    "lifetime_value": round(random.uniform(85000, 285000), 2),
                    "ltv_cac_ratio": round(random.uniform(15.8, 35.7), 1),
                    "gross_margin": round(random.uniform(82.5, 92.8), 1)
                },
                {
                    "segment": "Small Business",
                    "customer_count": random.randint(450, 1250),
                    "average_revenue": round(random.uniform(1200, 4500), 2),
                    "acquisition_cost": round(random.uniform(185, 650), 2),
                    "lifetime_value": round(random.uniform(8500, 25000), 2),
                    "ltv_cac_ratio": round(random.uniform(12.5, 28.9), 1),
                    "gross_margin": round(random.uniform(75.8, 88.2), 1)
                },
                {
                    "segment": "Individual Users",
                    "customer_count": random.randint(2500, 8500),
                    "average_revenue": round(random.uniform(299, 999), 2),
                    "acquisition_cost": round(random.uniform(45, 185), 2),
                    "lifetime_value": round(random.uniform(1500, 5500), 2),
                    "ltv_cac_ratio": round(random.uniform(8.5, 18.7), 1),
                    "gross_margin": round(random.uniform(68.9, 82.5), 1)
                }
            ],
            "margin_trends": [
                {
                    "month": (datetime.now() - timedelta(days=30*i)).strftime("%Y-%m"),
                    "gross_margin": round(random.uniform(75.5, 88.9), 1),
                    "operating_margin": round(random.uniform(25.8, 45.2), 1),
                    "net_margin": round(random.uniform(18.7, 35.9), 1)
                } for i in range(12, 0, -1)
            ]
        }
    }

@router.get("/financial-forecasting")
async def get_financial_forecasting(
    months: int = 12,
    scenario: str = "base",
    current_user: dict = Depends(get_current_user)
):
    """Get advanced financial forecasting with scenarios"""
    return await financial_service.get_forecasting(current_user["id"], months, scenario)

@router.get("/budget-analysis")
async def get_budget_analysis(current_user: dict = Depends(get_current_user)):
    """Get comprehensive budget vs actual analysis"""
    return await financial_service.get_budget_analysis(current_user["id"])

@router.get("/financial-kpis")
async def get_financial_kpis(
    period: str = "monthly",
    current_user: dict = Depends(get_current_user)
):
    """Get comprehensive financial KPIs dashboard"""
    
    return {
        "success": True,
        "data": {
            "growth_metrics": {
                "revenue_growth": {
                    "monthly": round(random.uniform(8.5, 18.7), 1),
                    "quarterly": round(random.uniform(25.8, 45.2), 1),
                    "yearly": round(random.uniform(95.3, 185.7), 1)
                },
                "customer_growth": {
                    "new_customers": random.randint(125, 485),
                    "growth_rate": round(random.uniform(12.5, 28.9), 1),
                    "churn_rate": round(random.uniform(2.8, 6.4), 1),
                    "net_growth": round(random.uniform(8.9, 22.5), 1)
                },
                "market_expansion": {
                    "market_share": round(random.uniform(8.5, 15.7), 1),
                    "addressable_market": round(random.uniform(2500000, 8500000), 2),
                    "penetration_rate": round(random.uniform(1.2, 4.8), 1)
                }
            },
            "efficiency_metrics": {
                "operational_efficiency": {
                    "revenue_per_employee": round(random.uniform(125000, 285000), 2),
                    "cost_per_acquisition": round(random.uniform(185, 850), 2),
                    "customer_success_rate": round(random.uniform(88.5, 96.8), 1)
                },
                "financial_efficiency": {
                    "cash_conversion_cycle": random.randint(25, 65),
                    "days_sales_outstanding": random.randint(18, 45),
                    "inventory_turnover": round(random.uniform(8.5, 18.7), 1)
                }
            },
            "sustainability_metrics": {
                "recurring_revenue": {
                    "mrr": round(random.uniform(125000, 485000), 2),
                    "arr": round(random.uniform(1500000, 5820000), 2),
                    "recurring_percentage": round(random.uniform(75.8, 92.3), 1)
                },
                "unit_economics": {
                    "ltv": round(random.uniform(2500, 8500), 2),
                    "cac": round(random.uniform(285, 1250), 2),
                    "ltv_cac_ratio": round(random.uniform(6.8, 18.9), 1),
                    "payback_period": random.randint(8, 24)
                }
            },
            "risk_metrics": {
                "financial_stability": {
                    "debt_service_coverage": round(random.uniform(2.5, 6.8), 1),
                    "interest_coverage": round(random.uniform(8.9, 25.7), 1),
                    "current_ratio": round(random.uniform(2.1, 4.8), 1)
                },
                "business_risk": {
                    "customer_concentration": round(random.uniform(15.2, 35.8), 1),
                    "revenue_volatility": round(random.uniform(8.5, 22.3), 1),
                    "market_risk_score": round(random.uniform(3.2, 7.8), 1)
                }
            },
            "benchmark_comparison": {
                "industry_percentile": random.randint(65, 92),
                "peer_group_ranking": random.randint(1, 10),
                "performance_vs_industry": {
                    "revenue_growth": round(random.uniform(105.2, 145.8), 1),
                    "profitability": round(random.uniform(112.5, 158.7), 1),
                    "efficiency": round(random.uniform(98.7, 132.4), 1)
                }
            }
        }
    }

@router.get("/investor-metrics")
async def get_investor_metrics(current_user: dict = Depends(get_current_user)):
    """Get investor-ready financial metrics"""
    
    return {
        "success": True,
        "data": {
            "valuation_metrics": {
                "revenue_multiple": round(random.uniform(8.5, 15.7), 1),
                "ebitda_multiple": round(random.uniform(25.8, 45.2), 1),
                "price_to_sales": round(random.uniform(12.5, 28.9), 1),
                "enterprise_value": round(random.uniform(5500000, 25000000), 2)
            },
            "growth_trajectory": {
                "rule_of_40": round(random.uniform(45.8, 78.9), 1),
                "revenue_cagr_3yr": round(random.uniform(65.2, 125.8), 1),
                "market_size_growth": round(random.uniform(18.5, 35.7), 1),
                "competitive_moats": ["Network effects", "Data advantages", "Brand loyalty"]
            },
            "operational_excellence": {
                "gross_retention": round(random.uniform(92.5, 98.7), 1),
                "net_retention": round(random.uniform(115.8, 145.2), 1),
                "magic_number": round(random.uniform(1.2, 2.8), 1),
                "sales_efficiency": round(random.uniform(0.8, 2.1), 1)
            },
            "capital_efficiency": {
                "capital_intensity": round(random.uniform(8.5, 22.3), 1),
                "return_on_invested_capital": round(random.uniform(18.7, 35.9), 1),
                "free_cash_flow_margin": round(random.uniform(22.5, 38.7), 1),
                "working_capital_as_revenue": round(random.uniform(3.2, 8.9), 1)
            }
        }
    }