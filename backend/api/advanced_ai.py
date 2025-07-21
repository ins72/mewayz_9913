"""
Advanced AI API Routes

Provides API endpoints for advanced AI functionality including
machine learning, natural language processing, and AI automation.
"""

from fastapi import APIRouter, Depends, HTTPException
from typing import Dict, List, Optional, Any
from services.advanced_ai_service import advanced_ai_service
from core.auth import get_current_user

router = APIRouter(prefix="/api/advanced-ai", tags=["Advanced AI"])

@router.get("/capabilities")
async def get_ai_capabilities(current_user: dict = Depends(get_current_user)):
    """Get available advanced AI capabilities"""
    return await advanced_ai_service.get_ai_capabilities()

@router.get("/models")
async def get_available_models(current_user: dict = Depends(get_current_user)):
    """Get list of available AI models"""
    return await advanced_ai_service.get_available_models()

@router.post("/analyze")
async def analyze_data(
    data: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Perform advanced AI analysis on provided data"""
    user_id = current_user.get("user_id")
    return await advanced_ai_service.analyze_data(user_id, data)

@router.post("/predict")
async def make_prediction(
    prediction_data: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Make AI-powered predictions"""
    user_id = current_user.get("user_id")
    return await advanced_ai_service.make_prediction(user_id, prediction_data)

@router.get("/insights")
async def get_ai_insights(
    current_user: dict = Depends(get_current_user),
    category: Optional[str] = None
):
    """Get AI-generated insights"""
    user_id = current_user.get("user_id")
    return await advanced_ai_service.get_ai_insights(user_id, category)

@router.post("/train-model")
async def train_custom_model(
    training_data: Dict[str, Any],
    current_user: dict = Depends(get_current_user)
):
    """Train a custom AI model"""
    user_id = current_user.get("user_id")
    return await advanced_ai_service.train_custom_model(user_id, training_data)

@router.get("/training-status/{job_id}")
async def get_training_status(
    job_id: str,
    current_user: dict = Depends(get_current_user)
):
    """Get status of model training job"""
    return await advanced_ai_service.get_training_status(job_id)