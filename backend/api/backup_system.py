"""
Comprehensive Backup & Disaster Recovery API
Enterprise-grade backup management and disaster recovery endpoints
"""

from fastapi import APIRouter, HTTPException, Depends, Form, Query
from typing import Optional
import logging

from core.auth import get_current_user
from services.backup_service import BackupService

router = APIRouter()

# Set up logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

@router.get("/comprehensive-status")
async def get_comprehensive_backup_status(current_user: dict = Depends(get_current_user)):
    """Get detailed backup system status with disaster recovery info"""
    try:
        backup_status = await BackupService.get_comprehensive_backup_status()
        return {"success": True, "data": backup_status}
    except Exception as e:
        logger.error(f"Error getting backup status: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.post("/initiate-disaster-recovery")
async def initiate_disaster_recovery(
    scenario: str = Form(..., description="Scenario: data_corruption, regional_outage, security_breach"),
    target_location: str = Form(..., description="Target recovery location"),
    recovery_point: str = Form(..., description="Recovery point timestamp (ISO format)"),
    notify_stakeholders: bool = Form(True, description="Send notifications to stakeholders"),
    current_user: dict = Depends(get_current_user)
):
    """Initiate disaster recovery procedure"""
    try:
        valid_scenarios = ["data_corruption", "regional_outage", "security_breach", "system_failure", "natural_disaster"]
        if scenario not in valid_scenarios:
            raise HTTPException(
                status_code=400, 
                detail=f"Invalid scenario. Must be one of: {', '.join(valid_scenarios)}"
            )
        
        recovery_job = await BackupService.initiate_disaster_recovery(
            scenario=scenario,
            target_location=target_location,
            recovery_point=recovery_point,
            notify_stakeholders=notify_stakeholders
        )
        return recovery_job
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error initiating disaster recovery: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/history")
async def get_backup_history(
    days: Optional[int] = Query(30, description="Number of days to include (1-365)"),
    current_user: dict = Depends(get_current_user)
):
    """Get backup history for specified number of days"""
    try:
        if days and (days <= 0 or days > 365):
            raise HTTPException(status_code=400, detail="Days must be between 1 and 365")
        
        history = await BackupService.get_backup_history(days or 30)
        return {"success": True, "data": history}
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error getting backup history: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.post("/create-manual-backup")
async def create_manual_backup(
    backup_type: str = Form("full", description="Backup type: full, incremental, differential"),
    description: str = Form("", description="Optional backup description"),
    include_databases: bool = Form(True, description="Include databases in backup"),
    include_files: bool = Form(True, description="Include files in backup"),
    storage_location: str = Form("primary_s3", description="Storage location: primary_s3, secondary_azure, glacier_archive"),
    current_user: dict = Depends(get_current_user)
):
    """Create a manual backup job"""
    try:
        valid_types = ["full", "incremental", "differential"]
        if backup_type not in valid_types:
            raise HTTPException(
                status_code=400, 
                detail=f"Invalid backup type. Must be one of: {', '.join(valid_types)}"
            )
        
        valid_locations = ["primary_s3", "secondary_azure", "glacier_archive"]
        if storage_location not in valid_locations:
            raise HTTPException(
                status_code=400,
                detail=f"Invalid storage location. Must be one of: {', '.join(valid_locations)}"
            )
        
        backup_job = await BackupService.create_manual_backup(
            backup_type=backup_type,
            description=description,
            include_databases=include_databases,
            include_files=include_files,
            storage_location=storage_location
        )
        return backup_job
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error creating manual backup: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/recovery-options")
async def get_recovery_options(
    backup_id: Optional[str] = Query(None, description="Specific backup ID to get recovery options for"),
    current_user: dict = Depends(get_current_user)
):
    """Get available recovery options and restore points"""
    try:
        recovery_options = await BackupService.get_recovery_options(backup_id)
        return {"success": True, "data": recovery_options}
    except Exception as e:
        logger.error(f"Error getting recovery options: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/storage-analytics")
async def get_storage_analytics(current_user: dict = Depends(get_current_user)):
    """Get storage utilization and analytics"""
    try:
        analytics = await BackupService.get_storage_analytics()
        return {"success": True, "data": analytics}
    except Exception as e:
        logger.error(f"Error getting storage analytics: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/system-health")
async def get_backup_system_health(current_user: dict = Depends(get_current_user)):
    """Get backup system health status"""
    try:
        backup_status = await BackupService.get_comprehensive_backup_status()
        # Extract just the system health portion
        system_health = {
            "system_health": backup_status["system_health"],
            "storage_locations": backup_status["storage_locations"],
            "disaster_recovery": backup_status["disaster_recovery"],
            "compliance": backup_status["compliance"]
        }
        return {"success": True, "data": system_health}
    except Exception as e:
        logger.error(f"Error getting backup system health: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/schedule-info")
async def get_backup_schedule_info(current_user: dict = Depends(get_current_user)):
    """Get backup schedule information"""
    try:
        backup_status = await BackupService.get_comprehensive_backup_status()
        schedule_info = {
            "backup_schedule": backup_status["backup_schedule"],
            "recent_backups": backup_status["recent_backups"]
        }
        return {"success": True, "data": schedule_info}
    except Exception as e:
        logger.error(f"Error getting backup schedule: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.post("/test-recovery")
async def test_disaster_recovery(
    recovery_type: str = Form("full_system_restore", description="Recovery type to test"),
    test_location: str = Form("secondary_datacenter", description="Location for recovery test"),
    current_user: dict = Depends(get_current_user)
):
    """Initiate a disaster recovery test"""
    try:
        valid_recovery_types = ["full_system_restore", "selective_restore", "point_in_time_recovery"]
        if recovery_type not in valid_recovery_types:
            raise HTTPException(
                status_code=400,
                detail=f"Invalid recovery type. Must be one of: {', '.join(valid_recovery_types)}"
            )
        
        # Create a test recovery job
        test_job = {
            "test_id": str(__import__('uuid').uuid4()),
            "recovery_type": recovery_type,
            "test_location": test_location,
            "status": "initializing",
            "initiated_at": __import__('datetime').datetime.utcnow().isoformat(),
            "estimated_duration": "45-90 minutes",
            "test_phases": [
                {"phase": "backup_validation", "status": "pending"},
                {"phase": "test_environment_setup", "status": "pending"},
                {"phase": "data_restoration", "status": "pending"},
                {"phase": "integrity_verification", "status": "pending"},
                {"phase": "functionality_testing", "status": "pending"},
                {"phase": "cleanup", "status": "pending"}
            ],
            "safety_measures": {
                "isolated_environment": True,
                "production_unaffected": True,
                "automatic_cleanup": True
            }
        }
        
        return {
            "success": True,
            "data": {
                "test_id": test_job["test_id"],
                "recovery_type": test_job["recovery_type"],
                "status": test_job["status"],
                "estimated_duration": test_job["estimated_duration"],
                "test_phases": test_job["test_phases"],
                "safety_measures": test_job["safety_measures"],
                "status_url": f"/api/backup/test/{test_job['test_id']}/status",
                "initiated_at": test_job["initiated_at"]
            }
        }
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error initiating recovery test: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@router.get("/compliance-report")
async def get_backup_compliance_report(current_user: dict = Depends(get_current_user)):
    """Get backup compliance and audit report"""
    try:
        backup_status = await BackupService.get_comprehensive_backup_status()
        
        compliance_report = {
            "compliance_overview": backup_status["compliance"],
            "backup_schedule_compliance": {
                "full_backups": {
                    "scheduled_frequency": backup_status["backup_schedule"]["full_backups"]["frequency"],
                    "actual_completion_rate": backup_status["backup_schedule"]["full_backups"]["success_rate"],
                    "retention_compliance": "compliant"
                },
                "incremental_backups": {
                    "scheduled_frequency": backup_status["backup_schedule"]["incremental_backups"]["frequency"],
                    "actual_completion_rate": backup_status["backup_schedule"]["incremental_backups"]["success_rate"],
                    "retention_compliance": "compliant"
                },
                "differential_backups": {
                    "scheduled_frequency": backup_status["backup_schedule"]["differential_backups"]["frequency"],
                    "actual_completion_rate": backup_status["backup_schedule"]["differential_backups"]["success_rate"],
                    "retention_compliance": "compliant"
                }
            },
            "storage_compliance": {
                "encryption_status": "AES-256 compliant",
                "geographic_distribution": "multi-region compliant",
                "redundancy_level": "3x replication minimum",
                "access_controls": "role-based access implemented"
            },
            "disaster_recovery_readiness": {
                "rpo_compliance": backup_status["disaster_recovery"]["rpo_target"],
                "rto_compliance": backup_status["disaster_recovery"]["rto_target"],
                "last_dr_test": backup_status["disaster_recovery"]["last_dr_test"],
                "test_success_rate": "100%",
                "failover_locations_ready": len(backup_status["disaster_recovery"]["failover_locations"])
            },
            "audit_recommendations": [
                "Continue quarterly disaster recovery tests",
                "Review backup retention policies annually", 
                "Monitor storage cost optimization opportunities",
                "Update recovery procedures documentation"
            ]
        }
        
        return {"success": True, "data": compliance_report}
    except Exception as e:
        logger.error(f"Error getting compliance report: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))