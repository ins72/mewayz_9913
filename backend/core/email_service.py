"""
Email Service Manager
ElasticMail API + SMTP Email Support for Admin and Users  
"""

import os
import asyncio
import smtplib
from email.mime.text import MimeText
from email.mime.multipart import MimeMultipart
from email.mime.base import MimeBase
from email import encoders
import httpx
from typing import Dict, Any, Optional, List
from datetime import datetime
from core.logging import admin_logger
from core.database import get_database

class EmailServiceManager:
    """Complete email service with ElasticMail API and SMTP support"""
    
    def __init__(self):
        self.client = None
        self.smtp_settings = {}
        self.elasticmail_settings = {}
        
    async def initialize(self):
        """Initialize email services"""
        self.client = httpx.AsyncClient(timeout=30.0)
        await self._load_email_configuration()
        
    async def close(self):
        """Close HTTP connections"""
        if self.client:
            await self.client.aclose()
    
    async def _load_email_configuration(self):
        """Load email configuration from database"""
        try:
            db = get_database()
            config = await db.api_configuration.find_one({"type": "email_services"})
            
            if config:
                from core.security import security_manager
                
                # ElasticMail configuration
                self.elasticmail_settings = {
                    "api_key": self._get_decrypted_key(config, "elasticmail_api_key"),
                    "enabled": config.get("elasticmail_enabled", False)
                }
                
                # SMTP configuration
                self.smtp_settings = {
                    "host": config.get("smtp_host", "smtp.gmail.com"),
                    "port": config.get("smtp_port", 587),
                    "username": self._get_decrypted_key(config, "smtp_username"),
                    "password": self._get_decrypted_key(config, "smtp_password"),
                    "use_tls": config.get("smtp_use_tls", True),
                    "enabled": config.get("smtp_enabled", False)
                }
            else:
                # Load from environment as fallback
                self.elasticmail_settings = {
                    "api_key": os.getenv("ELASTICMAIL_API_KEY"),
                    "enabled": bool(os.getenv("ELASTICMAIL_API_KEY"))
                }
                
                self.smtp_settings = {
                    "host": os.getenv("SMTP_HOST", "smtp.gmail.com"),
                    "port": int(os.getenv("SMTP_PORT", "587")),
                    "username": os.getenv("SMTP_USERNAME"),
                    "password": os.getenv("SMTP_PASSWORD"),
                    "use_tls": bool(os.getenv("SMTP_USE_TLS", "true").lower() == "true"),
                    "enabled": bool(os.getenv("SMTP_USERNAME"))
                }
                
        except Exception as e:
            admin_logger.log_system_event("EMAIL_CONFIG_LOAD_FAILED", {
                "error": str(e)
            }, "ERROR")
    
    def _get_decrypted_key(self, config: Dict, key: str) -> Optional[str]:
        """Decrypt API key from configuration"""
        try:
            if key in config and config[key]:
                from core.security import security_manager
                return security_manager.decrypt_sensitive_data(config[key])
        except Exception as e:
            admin_logger.log_system_event("EMAIL_KEY_DECRYPTION_FAILED", {
                "key": key,
                "error": str(e)
            }, "ERROR")
        return None
    
    async def send_email(self, to_email: str, subject: str, content: str, 
                        content_type: str = "html", attachments: List[Dict] = None,
                        from_email: str = None, from_name: str = None) -> Dict[str, Any]:
        """Send email using best available service"""
        
        # Try ElasticMail first if configured
        if self.elasticmail_settings.get("enabled") and self.elasticmail_settings.get("api_key"):
            result = await self._send_via_elasticmail(
                to_email, subject, content, content_type, attachments, from_email, from_name
            )
            if result.get("success"):
                return result
        
        # Fall back to SMTP if ElasticMail fails or is not configured
        if self.smtp_settings.get("enabled") and self.smtp_settings.get("username"):
            return await self._send_via_smtp(
                to_email, subject, content, content_type, attachments, from_email, from_name
            )
        
        # No email service configured
        return {
            "success": False,
            "error": "No email service configured"
        }
    
    async def _send_via_elasticmail(self, to_email: str, subject: str, content: str,
                                   content_type: str, attachments: List[Dict] = None,
                                   from_email: str = None, from_name: str = None) -> Dict[str, Any]:
        """Send email via ElasticMail API"""
        try:
            start_time = datetime.now()
            
            headers = {
                "X-ElasticEmail-ApiKey": self.elasticmail_settings["api_key"],
                "Content-Type": "application/json"
            }
            
            # Prepare email data
            email_data = {
                "Recipients": {
                    "To": [to_email]
                },
                "Content": {
                    "Subject": subject,
                    "From": from_email or "noreply@mewayz.com",
                    "FromName": from_name or "Mewayz Platform"
                }
            }
            
            if content_type.lower() == "html":
                email_data["Content"]["Body"] = [{
                    "ContentType": "HTML",
                    "Content": content
                }]
            else:
                email_data["Content"]["Body"] = [{
                    "ContentType": "PlainText", 
                    "Content": content
                }]
            
            # Add attachments if provided
            if attachments:
                email_data["Content"]["Attachments"] = []
                for attachment in attachments:
                    email_data["Content"]["Attachments"].append({
                        "BinaryContent": attachment.get("content"),
                        "Name": attachment.get("filename"),
                        "ContentType": attachment.get("content_type", "application/octet-stream")
                    })
            
            response = await self.client.post(
                "https://api.elasticemail.com/v4/emails",
                headers=headers,
                json=email_data
            )
            
            response_time = (datetime.now() - start_time).total_seconds() * 1000
            
            admin_logger.log_external_api_call(
                service="elasticmail",
                endpoint="/v4/emails",
                status=response.status_code,
                response_time=response_time
            )
            
            if response.status_code == 201:
                result = response.json()
                
                # Log email sent
                await self._log_email_sent("elasticmail", to_email, subject, True)
                
                return {
                    "success": True,
                    "service": "elasticmail",
                    "message_id": result.get("MessageID"),
                    "to": to_email,
                    "subject": subject,
                    "sent_at": datetime.utcnow().isoformat()
                }
            else:
                error_text = response.text
                return {
                    "success": False,
                    "service": "elasticmail",
                    "error": f"ElasticMail API error: {response.status_code} - {error_text}"
                }
                
        except Exception as e:
            admin_logger.log_external_api_call(
                service="elasticmail",
                endpoint="/v4/emails",
                status=0,
                response_time=0,
                error=str(e)
            )
            
            return {
                "success": False,
                "service": "elasticmail",
                "error": str(e)
            }
    
    async def _send_via_smtp(self, to_email: str, subject: str, content: str,
                           content_type: str, attachments: List[Dict] = None,
                           from_email: str = None, from_name: str = None) -> Dict[str, Any]:
        """Send email via SMTP"""
        try:
            start_time = datetime.now()
            
            # Create message
            msg = MimeMultipart()
            msg['From'] = from_email or self.smtp_settings["username"]
            msg['To'] = to_email
            msg['Subject'] = subject
            
            # Add body
            if content_type.lower() == "html":
                msg.attach(MimeText(content, 'html'))
            else:
                msg.attach(MimeText(content, 'plain'))
            
            # Add attachments if provided
            if attachments:
                for attachment in attachments:
                    part = MimeBase('application', 'octet-stream')
                    part.set_payload(attachment.get("content", b""))
                    encoders.encode_base64(part)
                    part.add_header(
                        'Content-Disposition',
                        f'attachment; filename= {attachment.get("filename", "file")}'
                    )
                    msg.attach(part)
            
            # Send email in thread pool to avoid blocking
            loop = asyncio.get_event_loop()
            await loop.run_in_executor(None, self._send_smtp_email, msg, to_email)
            
            response_time = (datetime.now() - start_time).total_seconds() * 1000
            
            admin_logger.log_system_event("SMTP_EMAIL_SENT", {
                "to": to_email,
                "subject": subject,
                "response_time": response_time
            })
            
            # Log email sent
            await self._log_email_sent("smtp", to_email, subject, True)
            
            return {
                "success": True,
                "service": "smtp",
                "to": to_email,
                "subject": subject,
                "sent_at": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            admin_logger.log_system_event("SMTP_EMAIL_FAILED", {
                "to": to_email,
                "subject": subject,
                "error": str(e)
            }, "ERROR")
            
            return {
                "success": False,
                "service": "smtp",
                "error": str(e)
            }
    
    def _send_smtp_email(self, msg: MimeMultipart, to_email: str):
        """Send SMTP email (synchronous method for thread pool)"""
        server = None
        try:
            server = smtplib.SMTP(self.smtp_settings["host"], self.smtp_settings["port"])
            
            if self.smtp_settings["use_tls"]:
                server.starttls()
            
            server.login(self.smtp_settings["username"], self.smtp_settings["password"])
            text = msg.as_string()
            server.sendmail(self.smtp_settings["username"], to_email, text)
            
        finally:
            if server:
                server.quit()
    
    async def send_bulk_emails(self, recipients: List[Dict[str, Any]], 
                             subject: str, content: str, content_type: str = "html") -> Dict[str, Any]:
        """Send bulk emails"""
        successful_sends = 0
        failed_sends = 0
        errors = []
        
        for recipient in recipients:
            to_email = recipient.get("email")
            personalized_content = content
            
            # Replace personalization variables
            if recipient.get("name"):
                personalized_content = personalized_content.replace("{{name}}", recipient["name"])
            
            result = await self.send_email(
                to_email=to_email,
                subject=subject,
                content=personalized_content,
                content_type=content_type
            )
            
            if result.get("success"):
                successful_sends += 1
            else:
                failed_sends += 1
                errors.append({
                    "email": to_email,
                    "error": result.get("error")
                })
        
        return {
            "total_sent": successful_sends,
            "total_failed": failed_sends,
            "success_rate": (successful_sends / len(recipients)) * 100 if recipients else 0,
            "errors": errors
        }
    
    async def send_admin_notification(self, subject: str, message: str, 
                                    priority: str = "normal") -> Dict[str, Any]:
        """Send notification to admin"""
        admin_email = os.getenv("ADMIN_EMAIL", "admin@mewayz.com")
        
        html_content = f"""
        <html>
        <body>
            <h2>Mewayz Platform Admin Notification</h2>
            <p><strong>Priority:</strong> {priority.upper()}</p>
            <p><strong>Time:</strong> {datetime.utcnow().isoformat()}</p>
            <hr>
            <div>{message}</div>
        </body>
        </html>
        """
        
        return await self.send_email(
            to_email=admin_email,
            subject=f"[Mewayz Platform] {subject}",
            content=html_content,
            content_type="html",
            from_name="Mewayz Platform System"
        )
    
    async def send_user_welcome_email(self, user_email: str, user_name: str) -> Dict[str, Any]:
        """Send welcome email to new user"""
        subject = "Welcome to Mewayz Platform!"
        
        html_content = f"""
        <html>
        <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; text-align: center;">
                <h1 style="color: white; margin: 0;">Welcome to Mewayz!</h1>
            </div>
            <div style="padding: 40px;">
                <h2>Hi {user_name},</h2>
                <p>Welcome to the Mewayz Platform! We're thrilled to have you on board.</p>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3>What's Next?</h3>
                    <ul>
                        <li>Complete your profile setup</li>
                        <li>Connect your social media accounts</li>
                        <li>Explore our AI-powered tools</li>
                        <li>Set up your payment methods</li>
                    </ul>
                </div>
                
                <p>If you have any questions, our support team is here to help.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://mewayz.com/dashboard" style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Get Started</a>
                </div>
                
                <p style="color: #666; font-size: 14px;">
                    Best regards,<br>
                    The Mewayz Team
                </p>
            </div>
        </body>
        </html>
        """
        
        return await self.send_email(
            to_email=user_email,
            subject=subject,
            content=html_content,
            content_type="html"
        )
    
    async def _log_email_sent(self, service: str, to_email: str, subject: str, success: bool):
        """Log email activity to database"""
        try:
            db = get_database()
            
            email_log = {
                "service": service,
                "to_email": to_email,
                "subject": subject,
                "success": success,
                "sent_at": datetime.utcnow(),
                "platform": "mewayz"
            }
            
            await db.email_logs.insert_one(email_log)
            
        except Exception as e:
            admin_logger.log_system_event("EMAIL_LOG_FAILED", {
                "service": service,
                "to": to_email,
                "error": str(e)
            }, "WARNING")
    
    async def get_email_statistics(self, days: int = 30) -> Dict[str, Any]:
        """Get email sending statistics"""
        try:
            db = get_database()
            
            start_date = datetime.utcnow() - timedelta(days=days)
            
            # Get email stats
            pipeline = [
                {"$match": {"sent_at": {"$gte": start_date}}},
                {"$group": {
                    "_id": {"service": "$service", "success": "$success"},
                    "count": {"$sum": 1}
                }}
            ]
            
            stats = await db.email_logs.aggregate(pipeline).to_list(length=100)
            
            # Process stats
            result = {
                "period_days": days,
                "total_sent": 0,
                "successful": 0,
                "failed": 0,
                "by_service": {}
            }
            
            for stat in stats:
                service = stat["_id"]["service"]
                success = stat["_id"]["success"]
                count = stat["count"]
                
                result["total_sent"] += count
                
                if success:
                    result["successful"] += count
                else:
                    result["failed"] += count
                
                if service not in result["by_service"]:
                    result["by_service"][service] = {"sent": 0, "successful": 0, "failed": 0}
                
                result["by_service"][service]["sent"] += count
                if success:
                    result["by_service"][service]["successful"] += count
                else:
                    result["by_service"][service]["failed"] += count
            
            result["success_rate"] = (result["successful"] / result["total_sent"]) * 100 if result["total_sent"] > 0 else 0
            
            return result
            
        except Exception as e:
            admin_logger.log_system_event("EMAIL_STATS_FAILED", {
                "error": str(e)
            }, "ERROR")
            return {}
    
    async def get_status(self) -> Dict[str, Any]:
        """Get email service status"""
        status = {
            "elasticmail": {
                "enabled": self.elasticmail_settings.get("enabled", False),
                "configured": bool(self.elasticmail_settings.get("api_key")),
                "status": "ready" if self.elasticmail_settings.get("api_key") else "not_configured"
            },
            "smtp": {
                "enabled": self.smtp_settings.get("enabled", False),
                "configured": bool(self.smtp_settings.get("username")),
                "host": self.smtp_settings.get("host"),
                "port": self.smtp_settings.get("port"),
                "status": "ready" if self.smtp_settings.get("username") else "not_configured"
            },
            "overall_status": "operational" if (
                self.elasticmail_settings.get("api_key") or self.smtp_settings.get("username")
            ) else "not_configured",
            "last_checked": datetime.utcnow().isoformat()
        }
        
        return status

# Global email service manager
email_service_manager = EmailServiceManager()