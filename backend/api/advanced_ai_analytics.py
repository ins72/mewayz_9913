"""
Advanced AI Analytics API Endpoints
Predictive insights, trend analysis, and intelligent recommendations
"""
from fastapi import APIRouter, HTTPException, Depends, status, Query
from typing import Dict, Any, List, Optional
from datetime import datetime
import uuid

from core.auth import get_current_user, require_auth
from core.ai_analytics_engine import ai_analytics_engine, AnalyticsType
from core.professional_logger import professional_logger, LogLevel, LogCategory

router = APIRouter(
    prefix="/api/ai-analytics",
    tags=["Advanced AI Analytics"],
    dependencies=[Depends(require_auth)]
)

@router.post("/insights/generate", response_model=Dict[str, Any])
async def generate_predictive_insights(
    timeframe: int = Query(30, description="Analysis timeframe in days", ge=7, le=365),
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Generate AI-powered predictive insights for the user
    
    - **timeframe**: Number of days to analyze (7-365)
    - Returns comprehensive insights including revenue trends, engagement analysis, churn risk, and content performance
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Generate predictive insights
        insights = await ai_analytics_engine.generate_predictive_insights(
            user_id=user_id,
            timeframe=timeframe
        )
        
        # Convert insights to dict format
        insight_dicts = []
        for insight in insights:
            insight_dict = {
                "insight_id": insight.insight_id,
                "type": insight.type.value,
                "title": insight.title,
                "description": insight.description,
                "confidence": insight.confidence,
                "impact_level": insight.impact_level,
                "recommendations": insight.recommendations,
                "data_points": insight.data_points,
                "created_at": insight.created_at.isoformat(),
                "expires_at": insight.expires_at.isoformat() if insight.expires_at else None
            }
            insight_dicts.append(insight_dict)
        
        await professional_logger.log(
            LogLevel.INFO, LogCategory.AI_SERVICE,
            f"Generated {len(insights)} AI insights for user",
            user_id=user_id,
            details={"insights_count": len(insights), "timeframe_days": timeframe}
        )
        
        return {
            "success": True,
            "insights": insight_dicts,
            "total_insights": len(insights),
            "timeframe_days": timeframe,
            "generated_at": datetime.utcnow().isoformat()
        }
        
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to generate AI insights: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to generate insights: {str(e)}"
        )

@router.get("/insights", response_model=Dict[str, Any])
async def get_user_insights(
    limit: int = Query(10, description="Maximum number of insights to return", ge=1, le=50),
    insight_type: Optional[str] = Query(None, description="Filter by insight type"),
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Retrieve stored AI insights for the current user
    
    - **limit**: Maximum number of insights to return (1-50)
    - **insight_type**: Optional filter by insight type (predictive, trend, anomaly, recommendation, sentiment, forecasting)
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Get insights from database
        insights = await ai_analytics_engine.get_user_insights(
            user_id=user_id,
            limit=limit
        )
        
        # Filter by type if specified
        if insight_type:
            valid_types = [t.value for t in AnalyticsType]
            if insight_type not in valid_types:
                raise HTTPException(
                    status_code=status.HTTP_400_BAD_REQUEST,
                    detail=f"Invalid insight type. Valid types: {', '.join(valid_types)}"
                )
            insights = [insight for insight in insights if insight.get("type") == insight_type]
        
        return {
            "success": True,
            "insights": insights,
            "total_insights": len(insights),
            "filtered_by_type": insight_type,
            "retrieved_at": datetime.utcnow().isoformat()
        }
        
    except HTTPException:
        raise
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to get user insights: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to retrieve insights: {str(e)}"
        )

@router.post("/insights/anomaly-detection", response_model=Dict[str, Any])
async def generate_anomaly_detection(
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Generate anomaly detection analysis for user data patterns
    
    - Detects unusual patterns in user behavior, revenue, engagement, etc.
    - Returns actionable insights about data anomalies
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Generate anomaly detection insights
        anomalies = await ai_analytics_engine.generate_anomaly_detection(user_id=user_id)
        
        # Convert insights to dict format
        anomaly_dicts = []
        for anomaly in anomalies:
            anomaly_dict = {
                "insight_id": anomaly.insight_id,
                "type": anomaly.type.value,
                "title": anomaly.title,
                "description": anomaly.description,
                "confidence": anomaly.confidence,
                "impact_level": anomaly.impact_level,
                "recommendations": anomaly.recommendations,
                "data_points": anomaly.data_points,
                "created_at": anomaly.created_at.isoformat(),
                "expires_at": anomaly.expires_at.isoformat() if anomaly.expires_at else None
            }
            anomaly_dicts.append(anomaly_dict)
        
        await professional_logger.log(
            LogLevel.INFO, LogCategory.AI_SERVICE,
            f"Generated {len(anomalies)} anomaly detection insights",
            user_id=user_id,
            details={"anomalies_count": len(anomalies)}
        )
        
        return {
            "success": True,
            "anomalies": anomaly_dicts,
            "total_anomalies": len(anomalies),
            "generated_at": datetime.utcnow().isoformat()
        }
        
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to generate anomaly detection: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to generate anomaly detection: {str(e)}"
        )

@router.put("/insights/{insight_id}/mark-viewed", response_model=Dict[str, Any])
async def mark_insight_viewed(
    insight_id: str,
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Mark an insight as viewed by the user
    
    - **insight_id**: The ID of the insight to mark as viewed
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Update insight in database
        from core.database import get_database
        db = get_database()
        
        result = await db.ai_insights.update_one(
            {
                "insight_id": insight_id,
                "user_id": user_id
            },
            {
                "$set": {
                    "viewed": True,
                    "viewed_at": datetime.utcnow()
                }
            }
        )
        
        if result.modified_count == 0:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Insight not found or already viewed"
            )
        
        await professional_logger.log(
            LogLevel.INFO, LogCategory.AI_SERVICE,
            f"Insight marked as viewed: {insight_id}",
            user_id=user_id,
            details={"insight_id": insight_id}
        )
        
        return {
            "success": True,
            "message": "Insight marked as viewed",
            "insight_id": insight_id,
            "viewed_at": datetime.utcnow().isoformat()
        }
        
    except HTTPException:
        raise
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to mark insight as viewed: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to mark insight as viewed: {str(e)}"
        )

@router.put("/insights/{insight_id}/mark-acted", response_model=Dict[str, Any])
async def mark_insight_acted_upon(
    insight_id: str,
    action_taken: str = Query(..., description="Description of action taken"),
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Mark an insight as acted upon by the user
    
    - **insight_id**: The ID of the insight to mark as acted upon
    - **action_taken**: Description of the action taken based on the insight
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Update insight in database
        from core.database import get_database
        db = get_database()
        
        result = await db.ai_insights.update_one(
            {
                "insight_id": insight_id,
                "user_id": user_id
            },
            {
                "$set": {
                    "acted_upon": True,
                    "action_taken": action_taken,
                    "acted_at": datetime.utcnow()
                }
            }
        )
        
        if result.modified_count == 0:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Insight not found"
            )
        
        await professional_logger.log(
            LogLevel.INFO, LogCategory.AI_SERVICE,
            f"Insight marked as acted upon: {insight_id}",
            user_id=user_id,
            details={"insight_id": insight_id, "action_taken": action_taken}
        )
        
        return {
            "success": True,
            "message": "Insight marked as acted upon",
            "insight_id": insight_id,
            "action_taken": action_taken,
            "acted_at": datetime.utcnow().isoformat()
        }
        
    except HTTPException:
        raise
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to mark insight as acted upon: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to mark insight as acted upon: {str(e)}"
        )

@router.get("/analytics/summary", response_model=Dict[str, Any])
async def get_analytics_summary(
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Get AI analytics summary for the current user
    
    - Returns overview of insights, performance metrics, and recommendations
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Get insights from database
        from core.database import get_database
        db = get_database()
        
        # Count insights by type
        pipeline = [
            {"$match": {"user_id": user_id}},
            {"$group": {
                "_id": "$type",
                "count": {"$sum": 1},
                "avg_confidence": {"$avg": "$confidence"}
            }}
        ]
        
        insight_stats = await db.ai_insights.aggregate(pipeline).to_list(length=None)
        
        # Get recent insights
        recent_insights = await db.ai_insights.find({
            "user_id": user_id
        }).sort("created_at", -1).limit(5).to_list(length=5)
        
        # Convert ObjectId to string
        for insight in recent_insights:
            insight["_id"] = str(insight["_id"])
            if "created_at" in insight:
                insight["created_at"] = insight["created_at"].isoformat()
            if insight.get("expires_at"):
                insight["expires_at"] = insight["expires_at"].isoformat()
        
        # Calculate total insights and viewed percentage
        total_insights = await db.ai_insights.count_documents({"user_id": user_id})
        viewed_insights = await db.ai_insights.count_documents({"user_id": user_id, "viewed": True})
        acted_insights = await db.ai_insights.count_documents({"user_id": user_id, "acted_upon": True})
        
        viewed_percentage = (viewed_insights / total_insights * 100) if total_insights > 0 else 0
        action_percentage = (acted_insights / total_insights * 100) if total_insights > 0 else 0
        
        return {
            "success": True,
            "summary": {
                "total_insights": total_insights,
                "viewed_insights": viewed_insights,
                "acted_insights": acted_insights,
                "viewed_percentage": round(viewed_percentage, 1),
                "action_percentage": round(action_percentage, 1),
                "insight_stats": insight_stats,
                "recent_insights": recent_insights
            },
            "generated_at": datetime.utcnow().isoformat()
        }
        
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to get analytics summary: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to get analytics summary: {str(e)}"
        )

@router.delete("/insights/{insight_id}", response_model=Dict[str, Any])
async def delete_insight(
    insight_id: str,
    current_user: Dict[str, Any] = Depends(get_current_user)
):
    """
    Delete a specific insight
    
    - **insight_id**: The ID of the insight to delete
    """
    try:
        user_id = current_user.get("user_id")
        if not user_id:
            raise HTTPException(
                status_code=status.HTTP_401_UNAUTHORIZED,
                detail="Invalid user authentication"
            )
        
        # Delete insight from database
        from core.database import get_database
        db = get_database()
        
        result = await db.ai_insights.delete_one({
            "insight_id": insight_id,
            "user_id": user_id
        })
        
        if result.deleted_count == 0:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Insight not found"
            )
        
        await professional_logger.log(
            LogLevel.INFO, LogCategory.AI_SERVICE,
            f"Insight deleted: {insight_id}",
            user_id=user_id,
            details={"insight_id": insight_id}
        )
        
        return {
            "success": True,
            "message": "Insight deleted successfully",
            "insight_id": insight_id,
            "deleted_at": datetime.utcnow().isoformat()
        }
        
    except HTTPException:
        raise
    except Exception as e:
        await professional_logger.log(
            LogLevel.ERROR, LogCategory.AI_SERVICE,
            f"Failed to delete insight: {str(e)}",
            error=e,
            user_id=current_user.get("user_id")
        )
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to delete insight: {str(e)}"
        )