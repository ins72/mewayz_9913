"""
Blog/Content API Routes
Professional Mewayz Platform
"""
from fastapi import APIRouter, HTTPException, Depends, status, Query
from pydantic import BaseModel
from typing import Optional, Dict, Any, List

from ..core.auth import get_current_active_user
from ..services.content_service import content_service

router = APIRouter()

class BlogPostCreate(BaseModel):
    title: str
    content: str
    excerpt: Optional[str] = None
    status: Optional[str] = "draft"  # draft, published, archived
    featured_image: Optional[str] = None
    categories: Optional[List[str]] = []
    tags: Optional[List[str]] = []
    meta_title: Optional[str] = None
    meta_description: Optional[str] = None
    focus_keyword: Optional[str] = None

class BlogPostUpdate(BaseModel):
    title: Optional[str] = None
    content: Optional[str] = None
    excerpt: Optional[str] = None
    status: Optional[str] = None
    featured_image: Optional[str] = None
    categories: Optional[List[str]] = None
    tags: Optional[List[str]] = None
    seo: Optional[Dict[str, Any]] = None

@router.post("/posts")
async def create_blog_post(
    post_data: BlogPostCreate,
    current_user: dict = Depends(get_current_active_user)
):
    """Create blog post with real database operations"""
    try:
        post = await content_service.create_blog_post(
            post_data=post_data.dict(),
            author_id=current_user["_id"]
        )
        
        return {
            "success": True,
            "message": "Blog post created successfully",
            "data": post
        }
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Failed to create blog post: {str(e)}"
        )

@router.get("/posts")
async def get_blog_posts(
    status_filter: Optional[str] = Query(None),
    author_id: Optional[str] = Query(None),
    categories: Optional[str] = Query(None),
    tags: Optional[str] = Query(None),
    search: Optional[str] = Query(None),
    limit: int = Query(10, ge=1, le=100),
    page: int = Query(1, ge=1),
    current_user: dict = Depends(get_current_active_user)
):
    """Get blog posts with real database operations"""
    try:
        filters = {}
        if status_filter:
            filters["status"] = status_filter
        if author_id:
            filters["author_id"] = author_id
        if categories:
            filters["categories"] = categories.split(",")
        if tags:
            filters["tags"] = tags.split(",")
        if search:
            filters["search"] = search
        
        skip = (page - 1) * limit
        
        result = await content_service.get_blog_posts(
            filters=filters,
            limit=limit,
            skip=skip
        )
        
        return {
            "success": True,
            "data": result
        }
    
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch blog posts"
        )

@router.get("/posts/{post_id}")
async def get_blog_post(
    post_id: str,
    current_user: dict = Depends(get_current_active_user)
):
    """Get single blog post with real database operations"""
    try:
        post = await content_service.get_blog_post(post_id=post_id)
        
        if not post:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Blog post not found"
            )
        
        return {
            "success": True,
            "data": post
        }
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch blog post"
        )

@router.get("/posts/slug/{slug}")
async def get_blog_post_by_slug(
    slug: str,
    current_user: dict = Depends(get_current_active_user)
):
    """Get blog post by slug with real database operations"""
    try:
        post = await content_service.get_blog_post(slug=slug)
        
        if not post:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Blog post not found"
            )
        
        return {
            "success": True,
            "data": post
        }
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch blog post"
        )

@router.put("/posts/{post_id}")
async def update_blog_post(
    post_id: str,
    post_data: BlogPostUpdate,
    current_user: dict = Depends(get_current_active_user)
):
    """Update blog post with real database operations"""
    try:
        update_data = post_data.dict(exclude_none=True)
        
        if not update_data:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail="No valid fields provided for update"
            )
        
        post = await content_service.update_blog_post(
            post_id=post_id,
            update_data=update_data,
            user_id=current_user["_id"]
        )
        
        return {
            "success": True,
            "message": "Blog post updated successfully",
            "data": post
        }
    
    except ValueError as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=str(e)
        )
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to update blog post"
        )

@router.delete("/posts/{post_id}")
async def delete_blog_post(
    post_id: str,
    current_user: dict = Depends(get_current_active_user)
):
    """Delete blog post with real database operations"""
    try:
        result = await content_service.delete_blog_post(
            post_id=post_id,
            user_id=current_user["_id"]
        )
        
        return {
            "success": True,
            "message": "Blog post deleted successfully",
            "data": result
        }
    
    except ValueError as e:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail=str(e)
        )
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete blog post"
        )

@router.get("/analytics")
async def get_blog_analytics(
    author_id: Optional[str] = Query(None),
    current_user: dict = Depends(get_current_active_user)
):
    """Get blog analytics with real database calculations"""
    try:
        # If no author_id specified, use current user
        # If author_id specified, check permissions
        if author_id and author_id != current_user["_id"]:
            if not current_user.get("is_admin", False):
                raise HTTPException(
                    status_code=status.HTTP_403_FORBIDDEN,
                    detail="Access denied"
                )
        
        target_author_id = author_id or current_user["_id"]
        
        analytics = await content_service.get_blog_analytics(author_id=target_author_id)
        
        return {
            "success": True,
            "data": analytics
        }
    
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch blog analytics"
        )