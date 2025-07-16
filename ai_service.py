#!/usr/bin/env python3
"""
AI Integration Service for Mewayz Laravel Application
Uses emergentintegrations library for OpenAI, Anthropic, and Gemini integration
"""

import asyncio
import json
import sys
import os
from pathlib import Path

# Add the project root to the Python path
sys.path.insert(0, str(Path(__file__).parent))

try:
    from emergentintegrations.llm.chat import LlmChat, UserMessage
except ImportError as e:
    print(json.dumps({"error": f"Failed to import emergentintegrations: {e}"}))
    sys.exit(1)

class MewayzAIService:
    def __init__(self, api_key, provider="openai", model="gpt-4o"):
        self.api_key = api_key
        self.provider = provider
        self.model = model
        
        # Model mapping
        self.models = {
            "openai": {
                "gpt-4o": "gpt-4o",
                "gpt-4o-mini": "gpt-4o-mini",
                "gpt-4": "gpt-4",
                "gpt-3.5-turbo": "gpt-3.5-turbo",
                "o1": "o1",
                "o1-mini": "o1-mini",
            },
            "anthropic": {
                "claude-3-5-sonnet": "claude-3-5-sonnet-20241022",
                "claude-3-5-haiku": "claude-3-5-haiku-20241022",
                "claude-sonnet-4": "claude-sonnet-4-20250514",
                "claude-opus-4": "claude-opus-4-20250514",
            },
            "gemini": {
                "gemini-2.0-flash": "gemini-2.0-flash",
                "gemini-1.5-pro": "gemini-1.5-pro",
                "gemini-1.5-flash": "gemini-1.5-flash",
                "gemini-2.0-flash-lite": "gemini-2.0-flash-lite",
            }
        }
    
    def get_model_name(self, provider, model):
        """Get the correct model name for the provider"""
        if provider in self.models and model in self.models[provider]:
            return self.models[provider][model]
        return model  # Return as-is if not found
    
    async def chat(self, message, session_id="default", system_message=None):
        """Send a chat message and get response"""
        try:
            # Default system message for Mewayz
            if system_message is None:
                system_message = "You are a helpful AI assistant for Mewayz, an all-in-one business platform. You help users with social media management, course creation, e-commerce, CRM, and marketing tasks. Be professional, helpful, and concise."
            
            # Create chat instance
            chat = LlmChat(
                api_key=self.api_key,
                session_id=session_id,
                system_message=system_message
            )
            
            # Configure model
            model_name = self.get_model_name(self.provider, self.model)
            chat.with_model(self.provider, model_name)
            chat.with_max_tokens(8192)
            
            # Create user message
            user_message = UserMessage(text=message)
            
            # Send message and get response
            response = await chat.send_message(user_message)
            
            return {
                "success": True,
                "response": response,
                "provider": self.provider,
                "model": model_name,
                "session_id": session_id
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "provider": self.provider,
                "model": self.model,
                "session_id": session_id
            }
    
    async def generate_content(self, content_type, prompt, session_id="content_gen"):
        """Generate content based on type"""
        try:
            # Content-specific system messages
            system_messages = {
                "social_post": "You are a social media content creator. Generate engaging, concise social media posts that are platform-appropriate and include relevant hashtags.",
                "email": "You are an email marketing specialist. Create compelling email content that drives engagement and conversions.",
                "blog_post": "You are a professional blog writer. Create well-structured, informative blog posts with proper headings and engaging content.",
                "product_description": "You are a product marketing specialist. Write compelling product descriptions that highlight features, benefits, and drive sales.",
                "ad_copy": "You are an advertising copywriter. Create persuasive ad copy that captures attention and drives action."
            }
            
            system_message = system_messages.get(content_type, "You are a helpful content creator.")
            
            # Create chat instance
            chat = LlmChat(
                api_key=self.api_key,
                session_id=session_id,
                system_message=system_message
            )
            
            # Configure model
            model_name = self.get_model_name(self.provider, self.model)
            chat.with_model(self.provider, model_name)
            chat.with_max_tokens(4096)
            
            # Create user message
            user_message = UserMessage(text=prompt)
            
            # Send message and get response
            response = await chat.send_message(user_message)
            
            return {
                "success": True,
                "content": response,
                "content_type": content_type,
                "provider": self.provider,
                "model": model_name
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "content_type": content_type,
                "provider": self.provider,
                "model": self.model
            }
    
    async def analyze_text(self, text, analysis_type="sentiment", session_id="text_analysis"):
        """Analyze text for sentiment, readability, keywords, or summary"""
        try:
            # Analysis-specific prompts
            analysis_prompts = {
                "sentiment": f"Analyze the sentiment of this text and provide a score from -1 (very negative) to 1 (very positive), along with a brief explanation:\n\n{text}",
                "readability": f"Analyze the readability of this text and provide a score from 1 (very difficult) to 10 (very easy), along with suggestions for improvement:\n\n{text}",
                "keywords": f"Extract the main keywords and key phrases from this text, ranked by importance:\n\n{text}",
                "summary": f"Provide a concise summary of this text in 2-3 sentences:\n\n{text}"
            }
            
            prompt = analysis_prompts.get(analysis_type, f"Analyze this text:\n\n{text}")
            
            # Create chat instance
            chat = LlmChat(
                api_key=self.api_key,
                session_id=session_id,
                system_message=f"You are a text analysis expert. Provide {analysis_type} analysis in a structured, professional format."
            )
            
            # Configure model
            model_name = self.get_model_name(self.provider, self.model)
            chat.with_model(self.provider, model_name)
            chat.with_max_tokens(2048)
            
            # Create user message
            user_message = UserMessage(text=prompt)
            
            # Send message and get response
            response = await chat.send_message(user_message)
            
            return {
                "success": True,
                "analysis": response,
                "analysis_type": analysis_type,
                "provider": self.provider,
                "model": model_name
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "analysis_type": analysis_type,
                "provider": self.provider,
                "model": self.model
            }
    
    async def get_recommendations(self, recommendation_type, data, session_id="recommendations"):
        """Get AI recommendations based on data"""
        try:
            # Recommendation-specific prompts
            recommendation_prompts = {
                "hashtags": f"Based on this content, suggest 10 relevant hashtags for maximum reach and engagement:\n\n{data}",
                "posting_times": f"Based on this audience data and content type, suggest optimal posting times:\n\n{data}",
                "content_ideas": f"Based on this information, suggest 5 creative content ideas:\n\n{data}",
                "audience_targeting": f"Based on this content and goals, suggest audience targeting strategies:\n\n{data}"
            }
            
            prompt = recommendation_prompts.get(recommendation_type, f"Provide recommendations for:\n\n{data}")
            
            # Create chat instance
            chat = LlmChat(
                api_key=self.api_key,
                session_id=session_id,
                system_message=f"You are a marketing strategy expert. Provide actionable {recommendation_type} recommendations."
            )
            
            # Configure model
            model_name = self.get_model_name(self.provider, self.model)
            chat.with_model(self.provider, model_name)
            chat.with_max_tokens(3072)
            
            # Create user message
            user_message = UserMessage(text=prompt)
            
            # Send message and get response
            response = await chat.send_message(user_message)
            
            return {
                "success": True,
                "recommendations": response,
                "recommendation_type": recommendation_type,
                "provider": self.provider,
                "model": model_name
            }
            
        except Exception as e:
            return {
                "success": False,
                "error": str(e),
                "recommendation_type": recommendation_type,
                "provider": self.provider,
                "model": self.model
            }

async def main():
    """Main function to handle CLI arguments"""
    if len(sys.argv) < 4:
        print(json.dumps({"error": "Usage: python ai_service.py <api_key> <action> <data...>"}))
        sys.exit(1)
    
    api_key = sys.argv[1]
    action = sys.argv[2]
    
    # Get provider and model from environment or use defaults
    provider = os.getenv("AI_PROVIDER", "openai")
    model = os.getenv("AI_MODEL", "gpt-4o")
    
    # Create AI service instance
    ai_service = MewayzAIService(api_key, provider, model)
    
    try:
        if action == "chat":
            message = sys.argv[3]
            session_id = sys.argv[4] if len(sys.argv) > 4 else "default"
            result = await ai_service.chat(message, session_id)
            
        elif action == "generate_content":
            content_type = sys.argv[3]
            prompt = sys.argv[4]
            session_id = sys.argv[5] if len(sys.argv) > 5 else "content_gen"
            result = await ai_service.generate_content(content_type, prompt, session_id)
            
        elif action == "analyze_text":
            text = sys.argv[3]
            analysis_type = sys.argv[4] if len(sys.argv) > 4 else "sentiment"
            session_id = sys.argv[5] if len(sys.argv) > 5 else "text_analysis"
            result = await ai_service.analyze_text(text, analysis_type, session_id)
            
        elif action == "get_recommendations":
            recommendation_type = sys.argv[3]
            data = sys.argv[4]
            session_id = sys.argv[5] if len(sys.argv) > 5 else "recommendations"
            result = await ai_service.get_recommendations(recommendation_type, data, session_id)
            
        else:
            result = {"error": f"Unknown action: {action}"}
        
        print(json.dumps(result))
        
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)

if __name__ == "__main__":
    asyncio.run(main())