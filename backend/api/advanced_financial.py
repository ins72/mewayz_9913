"""
Advanced Financial API Routes

Provides API endpoints for advanced financial analytics, forecasting,
and financial intelligence features.
"""

from fastapi import APIRouter, Depends, HTTPException
from typing import Dict, List, Optional, Any
from services.advanced_financial_service import advanced_financial_service
from core.auth import get_current_user

router = APIRouter(prefix="/api/advanced-financial", tags=["Advanced Financial"])

@router.get("/dashboard")
async def get_financial_dashboard(current_user: dict = Depends(get_current_user)):
    """Get comprehensive financial dashboard"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.get_financial_dashboard(user_id)

@router.get("/forecasting")
async def get_financial_forecast(
    current_user: dict = Depends(get_current_user),
    period: Optional[str] = "12m"
):
    """Get financial forecasting and predictions"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.get_financial_forecast(user_id, period)

@router.get("/risk-analysis")
async def get_risk_analysis(current_user: dict = Depends(get_current_user)):
    """Get financial risk analysis and assessment"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.get_risk_analysis(user_id)

@router.get("/portfolio-optimization")
async def get_portfolio_optimization(current_user: dict = Depends(get_current_user)):
    """Get portfolio optimization recommendations"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.get_portfolio_optimization(user_id)

@router.post("/scenario-analysis")
async def run_scenario_analysis(
    scenarios: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Run financial scenario analysis"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.run_scenario_analysis(user_id, scenarios)

@router.get("/market-intelligence")
async def get_market_intelligence(current_user: dict = Depends(get_current_user)):
    """Get market intelligence and trends"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.get_market_intelligence(user_id)

@router.get("/compliance-monitoring")
async def get_compliance_monitoring(current_user: dict = Depends(get_current_user)):
    """Get financial compliance monitoring"""
    user_id = current_user.get("user_id")
    return await advanced_financial_service.get_compliance_monitoring(user_id)