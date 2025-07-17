<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\TemplatePurchase;
use App\Models\TemplateReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TemplateMarketplaceController extends Controller
{
    /**
     * Get all templates with filtering and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Template::with(['category', 'creator:id,name', 'reviews'])
                ->where('status', 'approved')
                ->where('is_active', true);

            // Apply filters
            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->template_type) {
                $query->where('template_type', $request->template_type);
            }

            if ($request->price_range) {
                switch ($request->price_range) {
                    case 'free':
                        $query->where('price', 0);
                        break;
                    case 'paid':
                        $query->where('price', '>', 0);
                        break;
                    case 'premium':
                        $query->where('price', '>', 50);
                        break;
                }
            }

            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%')
                      ->orWhere('tags', 'like', '%' . $request->search . '%');
                });
            }

            // Apply sorting
            switch ($request->sort_by) {
                case 'popular':
                    $query->orderBy('download_count', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('average_rating', 'desc');
                    break;
                default:
                    $query->orderBy('featured', 'desc')->orderBy('created_at', 'desc');
            }

            $templates = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $templates,
                'message' => 'Templates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve templates'
            ], 500);
        }
    }

    /**
     * Get template categories
     */
    public function categories()
    {
        try {
            $categories = TemplateCategory::where('is_active', true)
                ->withCount('templates')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve categories: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories'
            ], 500);
        }
    }

    /**
     * Get featured templates
     */
    public function featured()
    {
        try {
            $templates = Template::with(['category', 'creator:id,name'])
                ->where('featured', true)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $templates,
                'message' => 'Featured templates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve featured templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured templates'
            ], 500);
        }
    }

    /**
     * Get template details
     */
    public function show(Request $request, $id)
    {
        try {
            $template = Template::with(['category', 'creator:id,name', 'reviews.user:id,name'])
                ->where('id', $id)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            // Check if user has purchased this template
            $hasPurchased = false;
            $user = Auth::user();
            if ($user) {
                $hasPurchased = TemplatePurchase::where('user_id', $user->id)
                    ->where('template_id', $id)
                    ->where('status', 'completed')
                    ->exists();
            }

            $templateData = $template->toArray();
            $templateData['has_purchased'] = $hasPurchased;

            return response()->json([
                'success' => true,
                'data' => $templateData,
                'message' => 'Template details retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve template details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve template details'
            ], 500);
        }
    }

    /**
     * Create a new template
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'template_type' => 'required|string|in:website,email,social,bio,course',
            'category_id' => 'required|exists:template_categories,id',
            'price' => 'required|numeric|min:0|max:999.99',
            'tags' => 'nullable|string|max:500',
            'preview_images' => 'nullable|array|max:5',
            'preview_images.*' => 'image|max:5120', // 5MB max per image
            'template_data' => 'required|json',
            'demo_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            
            // Handle preview images upload
            $previewImages = [];
            if ($request->hasFile('preview_images')) {
                foreach ($request->file('preview_images') as $image) {
                    $path = $image->store('templates/previews', 'public');
                    $previewImages[] = $path;
                }
            }

            $template = Template::create([
                'creator_id' => $user->id,
                'name' => $request->name,
                'description' => $request->description,
                'template_type' => $request->template_type,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'tags' => $request->tags,
                'preview_images' => $previewImages,
                'template_data' => json_decode($request->template_data, true),
                'demo_url' => $request->demo_url,
                'status' => 'pending',
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully and submitted for review',
                'data' => $template
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create template'
            ], 500);
        }
    }

    /**
     * Update an existing template
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:1000',
            'template_type' => 'sometimes|required|string|in:website,email,social,bio,course',
            'category_id' => 'sometimes|required|exists:template_categories,id',
            'price' => 'sometimes|required|numeric|min:0|max:999.99',
            'tags' => 'nullable|string|max:500',
            'preview_images' => 'nullable|array|max:5',
            'preview_images.*' => 'image|max:5120',
            'template_data' => 'sometimes|required|json',
            'demo_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            
            $template = Template::where('id', $id)
                ->where('creator_id', $user->id)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found or you do not have permission to edit it'
                ], 404);
            }

            $updateData = [];
            
            foreach (['name', 'description', 'template_type', 'category_id', 'price', 'tags', 'demo_url'] as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if ($request->has('template_data')) {
                $updateData['template_data'] = json_decode($request->template_data, true);
            }

            // Handle preview images upload
            if ($request->hasFile('preview_images')) {
                $previewImages = [];
                foreach ($request->file('preview_images') as $image) {
                    $path = $image->store('templates/previews', 'public');
                    $previewImages[] = $path;
                }
                $updateData['preview_images'] = $previewImages;
            }

            // Reset status to pending if template content is updated
            if ($request->has('template_data') || $request->hasFile('preview_images')) {
                $updateData['status'] = 'pending';
            }

            $template->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully',
                'data' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update template'
            ], 500);
        }
    }

    /**
     * Delete a template
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            $template = Template::where('id', $id)
                ->where('creator_id', $user->id)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found or you do not have permission to delete it'
                ], 404);
            }

            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete template'
            ], 500);
        }
    }

    /**
     * Purchase a template
     */
    public function purchase(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:stripe,paypal,wallet',
            'payment_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            
            $template = Template::where('id', $id)
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found or not available for purchase'
                ], 404);
            }

            // Check if user already purchased this template
            $existingPurchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $id)
                ->where('status', 'completed')
                ->first();

            if ($existingPurchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already purchased this template'
                ], 400);
            }

            // Create purchase record
            $purchase = TemplatePurchase::create([
                'user_id' => $user->id,
                'template_id' => $id,
                'creator_id' => $template->creator_id,
                'amount' => $template->price,
                'currency' => 'USD',
                'payment_method' => $request->payment_method,
                'payment_token' => $request->payment_token,
                'status' => 'processing',
            ]);

            // Process payment (implement actual payment processing here)
            $paymentResult = $this->processPayment($purchase);

            if ($paymentResult['success']) {
                $purchase->update([
                    'status' => 'completed',
                    'payment_reference' => $paymentResult['reference'],
                    'completed_at' => now(),
                ]);

                // Increment download count
                $template->increment('download_count');

                return response()->json([
                    'success' => true,
                    'message' => 'Template purchased successfully',
                    'data' => [
                        'purchase_id' => $purchase->id,
                        'template' => $template,
                        'download_url' => $template->download_url,
                    ]
                ]);
            } else {
                $purchase->update([
                    'status' => 'failed',
                    'failure_reason' => $paymentResult['error'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Payment failed: ' . $paymentResult['error']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Failed to purchase template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase template'
            ], 500);
        }
    }

    /**
     * Download a purchased template
     */
    public function download($id)
    {
        try {
            $user = Auth::user();
            
            // Check if user has purchased this template
            $purchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $id)
                ->where('status', 'completed')
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have not purchased this template'
                ], 403);
            }

            $template = Template::find($id);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template not found'
                ], 404);
            }

            // Return template data for download
            return response()->json([
                'success' => true,
                'data' => [
                    'template' => $template,
                    'template_data' => $template->template_data,
                    'download_count' => $template->download_count,
                ],
                'message' => 'Template downloaded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to download template: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to download template'
            ], 500);
        }
    }

    /**
     * Get user's purchased templates
     */
    public function myPurchases()
    {
        try {
            $user = Auth::user();
            
            $purchases = TemplatePurchase::where('user_id', $user->id)
                ->where('status', 'completed')
                ->with(['template' => function($query) {
                    $query->with('category');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $purchases,
                'message' => 'Purchased templates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve purchased templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve purchased templates'
            ], 500);
        }
    }

    /**
     * Get user's created templates
     */
    public function myTemplates()
    {
        try {
            $user = Auth::user();
            
            $templates = Template::where('creator_id', $user->id)
                ->with(['category', 'reviews'])
                ->withCount(['purchases' => function($query) {
                    $query->where('status', 'completed');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $templates,
                'message' => 'Your templates retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user templates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user templates'
            ], 500);
        }
    }

    /**
     * Add a review to a template
     */
    public function addReview(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $user = Auth::user();
            
            // Check if user has purchased this template
            $purchase = TemplatePurchase::where('user_id', $user->id)
                ->where('template_id', $id)
                ->where('status', 'completed')
                ->first();

            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only review templates you have purchased'
                ], 403);
            }

            // Check if user has already reviewed this template
            $existingReview = TemplateReview::where('user_id', $user->id)
                ->where('template_id', $id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this template'
                ], 400);
            }

            $review = TemplateReview::create([
                'user_id' => $user->id,
                'template_id' => $id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            // Update template average rating
            $this->updateTemplateRating($id);

            return response()->json([
                'success' => true,
                'message' => 'Review added successfully',
                'data' => $review
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to add review: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add review'
            ], 500);
        }
    }

    /**
     * Process payment for template purchase
     */
    private function processPayment($purchase)
    {
        // Mock payment processing
        // In a real implementation, integrate with Stripe, PayPal, etc.
        
        return [
            'success' => true,
            'reference' => 'PAY_' . strtoupper(uniqid()),
        ];
    }

    /**
     * Update template average rating
     */
    private function updateTemplateRating($templateId)
    {
        $averageRating = TemplateReview::where('template_id', $templateId)
            ->avg('rating');

        $reviewCount = TemplateReview::where('template_id', $templateId)
            ->count();

        Template::where('id', $templateId)->update([
            'average_rating' => round($averageRating, 2),
            'review_count' => $reviewCount,
        ]);
    }
}