import os
import json
from typing import Dict, List, Optional, Any
from openai import AsyncOpenAI
from fastapi import HTTPException
import asyncio
from datetime import datetime

class AISystem:
    def __init__(self):
        self.client = AsyncOpenAI(
            api_key=os.environ.get('OPENAI_API_KEY')
        )
    
    async def generate_content(self, prompt: str, content_type: str = "social_post", 
                             tone: str = "professional", max_tokens: int = 500) -> Dict[str, Any]:
        """Generate content using OpenAI GPT models"""
        try:
            system_prompts = {
                "social_post": "You are a professional social media content creator. Generate engaging, relevant social media posts that drive engagement.",
                "blog_article": "You are an expert content writer. Create comprehensive, well-structured articles with clear headings and valuable insights.",
                "email_campaign": "You are an email marketing specialist. Write compelling email content that converts and engages subscribers.",
                "product_description": "You are a skilled copywriter. Create persuasive product descriptions that highlight benefits and drive sales.",
                "course_content": "You are an educational content creator. Develop clear, structured learning materials that are easy to understand.",
                "website_copy": "You are a professional copywriter. Create compelling website content that converts visitors into customers.",
                "seo_content": "You are an SEO content specialist. Create content that ranks well in search engines while being valuable to readers."
            }
            
            system_prompt = system_prompts.get(content_type, system_prompts["social_post"])
            
            messages = [
                {"role": "system", "content": f"{system_prompt} Use a {tone} tone."},
                {"role": "user", "content": prompt}
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",  # Using the most cost-effective model
                messages=messages,
                max_tokens=max_tokens,
                temperature=0.7
            )
            
            generated_content = response.choices[0].message.content
            
            return {
                "success": True,
                "content": generated_content,
                "type": content_type,
                "tone": tone,
                "tokens_used": response.usage.total_tokens,
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "content": None
            }
    
    async def generate_image_prompt(self, description: str) -> Dict[str, Any]:
        """Generate optimized prompts for image generation"""
        try:
            messages = [
                {
                    "role": "system", 
                    "content": "You are an expert at creating detailed, artistic prompts for AI image generation. Transform basic descriptions into rich, detailed prompts that will generate beautiful, professional images."
                },
                {
                    "role": "user", 
                    "content": f"Create a detailed image generation prompt based on this description: {description}"
                }
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=200,
                temperature=0.8
            )
            
            enhanced_prompt = response.choices[0].message.content
            
            return {
                "success": True,
                "original_description": description,
                "enhanced_prompt": enhanced_prompt,
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "enhanced_prompt": description  # Fallback to original
            }
    
    async def analyze_content(self, content: str, analysis_type: str = "sentiment") -> Dict[str, Any]:
        """Analyze content for various purposes"""
        try:
            analysis_prompts = {
                "sentiment": "Analyze the sentiment of this content. Provide a sentiment score from -1 (very negative) to 1 (very positive) and explanation.",
                "seo": "Analyze this content for SEO. Suggest improvements, identify keywords, and rate SEO potential from 1-10.",
                "engagement": "Analyze this content for social media engagement potential. Rate from 1-10 and suggest improvements.",
                "readability": "Analyze the readability of this content. Provide a readability score and suggestions for improvement.",
                "brand_voice": "Analyze this content for brand voice consistency. Rate professionalism, tone, and brand alignment."
            }
            
            prompt = analysis_prompts.get(analysis_type, analysis_prompts["sentiment"])
            
            messages = [
                {"role": "system", "content": "You are an expert content analyst. Provide detailed, actionable insights."},
                {"role": "user", "content": f"{prompt}\n\nContent to analyze: {content}"}
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=300,
                temperature=0.3  # Lower temperature for analysis
            )
            
            analysis_result = response.choices[0].message.content
            
            return {
                "success": True,
                "analysis_type": analysis_type,
                "content": content,
                "analysis": analysis_result,
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "analysis": None
            }
    
    async def generate_hashtags(self, content: str, platform: str = "instagram", count: int = 10) -> Dict[str, Any]:
        """Generate relevant hashtags for social media content"""
        try:
            platform_guidance = {
                "instagram": "Generate Instagram hashtags that are popular, relevant, and have good engagement potential.",
                "twitter": "Generate Twitter hashtags that are trending and relevant to the content.",
                "linkedin": "Generate professional LinkedIn hashtags suitable for business content.",
                "tiktok": "Generate TikTok hashtags that are trending and likely to increase visibility.",
                "facebook": "Generate Facebook hashtags that encourage engagement and reach."
            }
            
            guidance = platform_guidance.get(platform, platform_guidance["instagram"])
            
            messages = [
                {
                    "role": "system", 
                    "content": f"You are a social media hashtag expert. {guidance} Return exactly {count} hashtags without explanations, each on a new line, starting with #."
                },
                {
                    "role": "user", 
                    "content": f"Generate {count} relevant hashtags for this {platform} content: {content}"
                }
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=200,
                temperature=0.6
            )
            
            hashtags_text = response.choices[0].message.content
            hashtags = [tag.strip() for tag in hashtags_text.split('\n') if tag.strip().startswith('#')]
            
            return {
                "success": True,
                "content": content,
                "platform": platform,
                "hashtags": hashtags[:count],  # Ensure we don't exceed requested count
                "count": len(hashtags[:count]),
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "hashtags": []
            }
    
    async def improve_content(self, content: str, improvement_type: str = "engagement") -> Dict[str, Any]:
        """Improve existing content based on specified criteria"""
        try:
            improvement_prompts = {
                "engagement": "Rewrite this content to maximize social media engagement while keeping the core message intact.",
                "clarity": "Rewrite this content to improve clarity and readability while maintaining the original tone.",
                "seo": "Rewrite this content to improve SEO performance while keeping it natural and engaging.",
                "professional": "Rewrite this content to sound more professional and polished.",
                "casual": "Rewrite this content to sound more casual and conversational.",
                "persuasive": "Rewrite this content to be more persuasive and compelling."
            }
            
            prompt = improvement_prompts.get(improvement_type, improvement_prompts["engagement"])
            
            messages = [
                {"role": "system", "content": "You are an expert content editor. Improve content while maintaining its core message and intent."},
                {"role": "user", "content": f"{prompt}\n\nOriginal content: {content}"}
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=600,
                temperature=0.7
            )
            
            improved_content = response.choices[0].message.content
            
            return {
                "success": True,
                "original_content": content,
                "improved_content": improved_content,
                "improvement_type": improvement_type,
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "improved_content": content  # Fallback to original
            }
    
    async def generate_course_content(self, topic: str, lesson_title: str, 
                                    difficulty: str = "beginner", duration: int = 15) -> Dict[str, Any]:
        """Generate educational course content"""
        try:
            messages = [
                {
                    "role": "system", 
                    "content": f"You are an expert educator creating {difficulty}-level course content. Structure lessons clearly with learning objectives, main content, examples, and key takeaways."
                },
                {
                    "role": "user", 
                    "content": f"Create a {duration}-minute lesson titled '{lesson_title}' for the topic '{topic}'. Include: 1) Learning objectives, 2) Main content with examples, 3) Key takeaways, 4) Practice questions."
                }
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=1000,
                temperature=0.6
            )
            
            lesson_content = response.choices[0].message.content
            
            return {
                "success": True,
                "topic": topic,
                "lesson_title": lesson_title,
                "difficulty": difficulty,
                "estimated_duration": duration,
                "content": lesson_content,
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "content": None
            }
    
    async def generate_email_sequence(self, purpose: str, audience: str, 
                                    sequence_length: int = 5) -> Dict[str, Any]:
        """Generate email marketing sequences"""
        try:
            messages = [
                {
                    "role": "system", 
                    "content": f"You are an email marketing expert. Create compelling email sequences that engage subscribers and drive action."
                },
                {
                    "role": "user", 
                    "content": f"Create a {sequence_length}-email sequence for '{purpose}' targeting '{audience}'. For each email, provide: subject line, preview text, and email body. Format as JSON with email1, email2, etc."
                }
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=1500,
                temperature=0.7
            )
            
            sequence_content = response.choices[0].message.content
            
            return {
                "success": True,
                "purpose": purpose,
                "audience": audience,
                "sequence_length": sequence_length,
                "emails": sequence_content,
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "emails": None
            }
    
    async def get_content_ideas(self, industry: str, content_type: str, count: int = 10) -> Dict[str, Any]:
        """Generate content ideas for various industries and types"""
        try:
            messages = [
                {
                    "role": "system", 
                    "content": f"You are a creative content strategist. Generate engaging, original content ideas that would perform well for the specified industry and content type."
                },
                {
                    "role": "user", 
                    "content": f"Generate {count} creative {content_type} content ideas for the {industry} industry. Each idea should be specific, actionable, and engaging. Format as a numbered list."
                }
            ]
            
            response = await self.client.chat.completions.create(
                model="gpt-4o-mini",
                messages=messages,
                max_tokens=600,
                temperature=0.8
            )
            
            ideas_content = response.choices[0].message.content
            ideas_list = [idea.strip() for idea in ideas_content.split('\n') if idea.strip() and not idea.strip().isspace()]
            
            return {
                "success": True,
                "industry": industry,
                "content_type": content_type,
                "ideas": ideas_list,
                "count": len(ideas_list),
                "timestamp": datetime.utcnow().isoformat()
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "ideas": []
            }

# Global AI system instance
ai_system = AISystem()