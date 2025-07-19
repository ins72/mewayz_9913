# Social Media & Email Marketing Integration System
# Handles X (Twitter), TikTok, and ElasticMail integrations

import os
import json
import requests
import tweepy
from typing import Dict, List, Optional, Any
from datetime import datetime, timedelta
from fastapi import HTTPException, status
import base64
import hashlib
import hmac
from urllib.parse import urlencode, quote

class XTwitterIntegration:
    """X (formerly Twitter) API Integration"""
    
    def __init__(self):
        self.api_key = os.getenv("X_API_KEY")
        self.api_secret = os.getenv("X_API_SECRET")
        
        if not self.api_key or not self.api_secret:
            raise ValueError("X API credentials not configured")
    
    def get_auth_url(self, callback_url: str) -> Dict[str, str]:
        """Generate OAuth URL for X authentication"""
        try:
            auth = tweepy.OAuth1UserHandler(
                self.api_key,
                self.api_secret,
                callback=callback_url
            )
            
            redirect_url = auth.get_authorization_url()
            request_token = auth.request_token["oauth_token"]
            
            return {
                "auth_url": redirect_url,
                "oauth_token": request_token,
                "success": True
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def authenticate_user(self, oauth_token: str, oauth_verifier: str) -> Dict[str, Any]:
        """Complete OAuth authentication and get user access tokens"""
        try:
            auth = tweepy.OAuth1UserHandler(
                self.api_key,
                self.api_secret
            )
            auth.request_token = {"oauth_token": oauth_token, "oauth_token_secret": ""}
            
            access_token, access_token_secret = auth.get_access_token(oauth_verifier)
            
            # Get user info
            api = tweepy.API(auth)
            user = api.verify_credentials()
            
            return {
                "success": True,
                "access_token": access_token,
                "access_token_secret": access_token_secret,
                "user_info": {
                    "id": user.id,
                    "username": user.screen_name,
                    "name": user.name,
                    "followers_count": user.followers_count,
                    "following_count": user.friends_count,
                    "profile_image": user.profile_image_url_https
                }
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def post_tweet(self, access_token: str, access_token_secret: str, text: str, media_ids: List[str] = None) -> Dict[str, Any]:
        """Post a tweet"""
        try:
            auth = tweepy.OAuth1UserHandler(
                self.api_key,
                self.api_secret,
                access_token,
                access_token_secret
            )
            
            api = tweepy.API(auth)
            
            if media_ids:
                tweet = api.update_status(status=text, media_ids=media_ids)
            else:
                tweet = api.update_status(status=text)
            
            return {
                "success": True,
                "tweet_id": tweet.id,
                "tweet_url": f"https://twitter.com/{tweet.user.screen_name}/status/{tweet.id}",
                "created_at": tweet.created_at.isoformat()
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def upload_media(self, access_token: str, access_token_secret: str, media_data: bytes, media_type: str) -> Dict[str, Any]:
        """Upload media to X"""
        try:
            auth = tweepy.OAuth1UserHandler(
                self.api_key,
                self.api_secret,
                access_token,
                access_token_secret
            )
            
            api = tweepy.API(auth)
            media = api.media_upload(filename="media", file=media_data)
            
            return {
                "success": True,
                "media_id": media.media_id,
                "media_url": media.media_url_https if hasattr(media, 'media_url_https') else None
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def get_user_tweets(self, access_token: str, access_token_secret: str, count: int = 10) -> Dict[str, Any]:
        """Get user's recent tweets"""
        try:
            auth = tweepy.OAuth1UserHandler(
                self.api_key,
                self.api_secret,
                access_token,
                access_token_secret
            )
            
            api = tweepy.API(auth)
            tweets = api.user_timeline(count=count, tweet_mode='extended')
            
            tweet_data = []
            for tweet in tweets:
                tweet_data.append({
                    "id": tweet.id,
                    "text": tweet.full_text,
                    "created_at": tweet.created_at.isoformat(),
                    "retweet_count": tweet.retweet_count,
                    "favorite_count": tweet.favorite_count,
                    "reply_count": tweet.reply_count if hasattr(tweet, 'reply_count') else 0,
                    "quote_count": tweet.quote_count if hasattr(tweet, 'quote_count') else 0
                })
            
            return {
                "success": True,
                "tweets": tweet_data
            }
        except Exception as e:
            return {"success": False, "error": str(e)}


class TikTokIntegration:
    """TikTok API Integration"""
    
    def __init__(self):
        self.client_key = os.getenv("TIKTOK_CLIENT_KEY")
        self.client_secret = os.getenv("TIKTOK_CLIENT_SECRET")
        
        if not self.client_key or not self.client_secret:
            raise ValueError("TikTok API credentials not configured")
        
        self.base_url = "https://open-api.tiktok.com"
    
    def get_auth_url(self, redirect_uri: str, scope: str = "user.info.basic,video.list") -> Dict[str, str]:
        """Generate OAuth URL for TikTok authentication"""
        try:
            csrf_token = base64.urlsafe_b64encode(os.urandom(32)).decode('utf-8')
            
            params = {
                "client_key": self.client_key,
                "scope": scope,
                "response_type": "code",
                "redirect_uri": redirect_uri,
                "state": csrf_token
            }
            
            auth_url = f"https://www.tiktok.com/auth/authorize/?" + urlencode(params)
            
            return {
                "auth_url": auth_url,
                "state": csrf_token,
                "success": True
            }
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def get_access_token(self, code: str, redirect_uri: str) -> Dict[str, Any]:
        """Exchange authorization code for access token"""
        try:
            url = f"{self.base_url}/oauth/access_token/"
            
            data = {
                "client_key": self.client_key,
                "client_secret": self.client_secret,
                "code": code,
                "grant_type": "authorization_code",
                "redirect_uri": redirect_uri
            }
            
            response = requests.post(url, json=data)
            result = response.json()
            
            if result.get("data"):
                return {
                    "success": True,
                    "access_token": result["data"]["access_token"],
                    "refresh_token": result["data"]["refresh_token"],
                    "expires_in": result["data"]["expires_in"],
                    "token_type": result["data"]["token_type"]
                }
            else:
                return {"success": False, "error": result.get("message", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def get_user_info(self, access_token: str) -> Dict[str, Any]:
        """Get TikTok user information"""
        try:
            url = f"{self.base_url}/user/info/"
            
            headers = {
                "Authorization": f"Bearer {access_token}",
                "Content-Type": "application/json"
            }
            
            data = {"fields": ["open_id", "union_id", "avatar_url", "display_name", "username"]}
            
            response = requests.post(url, json=data, headers=headers)
            result = response.json()
            
            if result.get("data") and result["data"].get("user"):
                user_data = result["data"]["user"]
                return {
                    "success": True,
                    "user_info": {
                        "open_id": user_data.get("open_id"),
                        "union_id": user_data.get("union_id"),
                        "username": user_data.get("username"),
                        "display_name": user_data.get("display_name"),
                        "avatar_url": user_data.get("avatar_url")
                    }
                }
            else:
                return {"success": False, "error": result.get("message", "Failed to get user info")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def get_user_videos(self, access_token: str, cursor: int = 0, max_count: int = 10) -> Dict[str, Any]:
        """Get user's TikTok videos"""
        try:
            url = f"{self.base_url}/video/list/"
            
            headers = {
                "Authorization": f"Bearer {access_token}",
                "Content-Type": "application/json"
            }
            
            data = {
                "fields": ["id", "title", "video_description", "duration", "cover_image_url", "create_time"],
                "cursor": cursor,
                "max_count": max_count
            }
            
            response = requests.post(url, json=data, headers=headers)
            result = response.json()
            
            if result.get("data"):
                return {
                    "success": True,
                    "videos": result["data"].get("videos", []),
                    "cursor": result["data"].get("cursor", 0),
                    "has_more": result["data"].get("has_more", False)
                }
            else:
                return {"success": False, "error": result.get("message", "Failed to get videos")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}


class ElasticMailIntegration:
    """ElasticMail API Integration for Email Marketing"""
    
    def __init__(self):
        self.api_key = os.getenv("ELASTICMAIL_API_KEY")
        
        if not self.api_key:
            raise ValueError("ElasticMail API key not configured")
        
        self.base_url = "https://api.elasticemail.com/v2"
    
    def send_email(self, to_email: str, subject: str, body: str, from_email: str = None, from_name: str = None) -> Dict[str, Any]:
        """Send individual email"""
        try:
            url = f"{self.base_url}/email/send"
            
            data = {
                "apikey": self.api_key,
                "to": to_email,
                "subject": subject,
                "bodyHtml": body,
                "from": from_email or "noreply@mewayz.com",
                "fromName": from_name or "Mewayz Platform"
            }
            
            response = requests.post(url, data=data)
            result = response.json()
            
            if result.get("success"):
                return {
                    "success": True,
                    "message_id": result.get("data", {}).get("messageid"),
                    "transaction_id": result.get("data", {}).get("transactionid")
                }
            else:
                return {"success": False, "error": result.get("error", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def send_bulk_email(self, recipients: List[str], subject: str, body: str, from_email: str = None, from_name: str = None) -> Dict[str, Any]:
        """Send bulk email campaign"""
        try:
            url = f"{self.base_url}/email/send"
            
            data = {
                "apikey": self.api_key,
                "to": ",".join(recipients),
                "subject": subject,
                "bodyHtml": body,
                "from": from_email or "noreply@mewayz.com",
                "fromName": from_name or "Mewayz Platform"
            }
            
            response = requests.post(url, data=data)
            result = response.json()
            
            if result.get("success"):
                return {
                    "success": True,
                    "message_id": result.get("data", {}).get("messageid"),
                    "transaction_id": result.get("data", {}).get("transactionid"),
                    "recipients_count": len(recipients)
                }
            else:
                return {"success": False, "error": result.get("error", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def create_contact(self, email: str, first_name: str = None, last_name: str = None, custom_fields: Dict[str, str] = None) -> Dict[str, Any]:
        """Add contact to ElasticMail"""
        try:
            url = f"{self.base_url}/contact/add"
            
            data = {
                "apikey": self.api_key,
                "email": email
            }
            
            if first_name:
                data["firstName"] = first_name
            if last_name:
                data["lastName"] = last_name
            if custom_fields:
                for key, value in custom_fields.items():
                    data[f"field_{key}"] = value
            
            response = requests.post(url, data=data)
            result = response.json()
            
            if result.get("success"):
                return {
                    "success": True,
                    "contact_id": result.get("data")
                }
            else:
                return {"success": False, "error": result.get("error", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def create_email_list(self, list_name: str, emails: List[str] = None) -> Dict[str, Any]:
        """Create email list"""
        try:
            url = f"{self.base_url}/list/add"
            
            data = {
                "apikey": self.api_key,
                "listName": list_name
            }
            
            response = requests.post(url, data=data)
            result = response.json()
            
            if result.get("success"):
                list_id = result.get("data")
                
                # Add emails to list if provided
                if emails and list_id:
                    for email in emails:
                        self.add_contact_to_list(email, list_name)
                
                return {
                    "success": True,
                    "list_id": list_id,
                    "list_name": list_name
                }
            else:
                return {"success": False, "error": result.get("error", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def add_contact_to_list(self, email: str, list_name: str) -> Dict[str, Any]:
        """Add contact to specific list"""
        try:
            url = f"{self.base_url}/list/addcontact"
            
            data = {
                "apikey": self.api_key,
                "listName": list_name,
                "email": email
            }
            
            response = requests.post(url, data=data)
            result = response.json()
            
            return {
                "success": result.get("success", False),
                "error": result.get("error") if not result.get("success") else None
            }
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def get_campaign_stats(self, campaign_id: str = None) -> Dict[str, Any]:
        """Get email campaign statistics"""
        try:
            url = f"{self.base_url}/campaign/list"
            
            data = {"apikey": self.api_key}
            
            response = requests.post(url, data=data)
            result = response.json()
            
            if result.get("success"):
                campaigns = result.get("data", [])
                
                if campaign_id:
                    # Filter for specific campaign
                    campaigns = [c for c in campaigns if c.get("campaignid") == campaign_id]
                
                return {
                    "success": True,
                    "campaigns": campaigns
                }
            else:
                return {"success": False, "error": result.get("error", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}
    
    def get_account_stats(self) -> Dict[str, Any]:
        """Get account statistics"""
        try:
            url = f"{self.base_url}/account/load"
            
            data = {"apikey": self.api_key}
            
            response = requests.post(url, data=data)
            result = response.json()
            
            if result.get("success"):
                account_data = result.get("data", {})
                return {
                    "success": True,
                    "account_info": {
                        "email": account_data.get("email"),
                        "credits": account_data.get("credit"),
                        "reputation": account_data.get("reputation"),
                        "total_emails_sent": account_data.get("totalEmailsSent"),
                        "monthly_emails_sent": account_data.get("monthlyEmailsSent"),
                        "daily_emails_sent": account_data.get("dailyEmailsSent")
                    }
                }
            else:
                return {"success": False, "error": result.get("error", "Unknown error")}
                
        except Exception as e:
            return {"success": False, "error": str(e)}


# Integration Manager
class SocialMediaEmailManager:
    """Unified manager for all social media and email integrations"""
    
    def __init__(self):
        try:
            self.x_integration = XTwitterIntegration()
        except ValueError:
            self.x_integration = None
        
        try:
            self.tiktok_integration = TikTokIntegration()
        except ValueError:
            self.tiktok_integration = None
        
        try:
            self.email_integration = ElasticMailIntegration()
        except ValueError:
            self.email_integration = None
    
    def get_available_integrations(self) -> Dict[str, bool]:
        """Check which integrations are available"""
        return {
            "x_twitter": self.x_integration is not None,
            "tiktok": self.tiktok_integration is not None,
            "elasticmail": self.email_integration is not None
        }
    
    def authenticate_platform(self, platform: str, **kwargs) -> Dict[str, Any]:
        """Authenticate with a specific platform"""
        if platform == "x" and self.x_integration:
            callback_url = kwargs.get("callback_url")
            return self.x_integration.get_auth_url(callback_url)
        elif platform == "tiktok" and self.tiktok_integration:
            redirect_uri = kwargs.get("redirect_uri")
            return self.tiktok_integration.get_auth_url(redirect_uri)
        else:
            return {"success": False, "error": f"Platform {platform} not available or not configured"}
    
    def post_content(self, platform: str, content: Dict[str, Any], credentials: Dict[str, str]) -> Dict[str, Any]:
        """Post content to a specific platform"""
        if platform == "x" and self.x_integration:
            return self.x_integration.post_tweet(
                credentials.get("access_token"),
                credentials.get("access_token_secret"),
                content.get("text"),
                content.get("media_ids")
            )
        else:
            return {"success": False, "error": f"Posting to {platform} not implemented yet"}
    
    def send_email_campaign(self, recipients: List[str], subject: str, body: str, sender_info: Dict[str, str] = None) -> Dict[str, Any]:
        """Send email campaign using ElasticMail"""
        if self.email_integration:
            sender_email = sender_info.get("email") if sender_info else None
            sender_name = sender_info.get("name") if sender_info else None
            
            if len(recipients) == 1:
                return self.email_integration.send_email(
                    recipients[0], subject, body, sender_email, sender_name
                )
            else:
                return self.email_integration.send_bulk_email(
                    recipients, subject, body, sender_email, sender_name
                )
        else:
            return {"success": False, "error": "ElasticMail integration not configured"}


# Export the manager instance
integration_manager = SocialMediaEmailManager()