<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;

class EcommerceController extends Controller
{
    public function getProducts(Request $request)
    {
        $products = Product::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function createProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products',
            'category' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'is_digital' => 'boolean',
        ]);

        $product = Product::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'sku' => $request->sku,
            'category' => $request->category,
            'images' => $request->images,
            'is_digital' => $request->is_digital ?? false,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    public function showProduct(Product $product)
    {
        // Check if user owns the product
        if ($product->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to product',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function updateProduct(Request $request, Product $product)
    {
        // Check if user owns the product
        if ($product->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to product',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'category' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'status' => 'in:active,inactive',
        ]);

        $product->update($request->only([
            'name', 'description', 'price', 'stock_quantity', 
            'sku', 'category', 'images', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    public function deleteProduct(Product $product)
    {
        // Check if user owns the product
        if ($product->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to product',
            ], 403);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function getOrders(Request $request)
    {
        $orders = ProductOrder::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function showOrder(ProductOrder $order)
    {
        // Check if user owns the order
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to order',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function updateOrderStatus(Request $request, ProductOrder $order)
    {
        // Check if user owns the order
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to order',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order,
        ]);
    }

    public function getAnalytics(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get orders data
        $orders = ProductOrder::where('user_id', $userId)->get();
        $totalRevenue = $orders->where('status', 'delivered')->sum('total_amount');
        $totalOrders = $orders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        // Get products data
        $products = Product::where('user_id', $userId)->get();
        $totalProducts = $products->count();
        $totalViews = $products->sum('view_count') ?? 0;
        $conversionRate = $totalViews > 0 ? (($totalOrders / $totalViews) * 100) : 0;
        
        // Top products
        $topProducts = Product::where('user_id', $userId)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'orders' => $product->orders_count,
                    'revenue' => $product->orders()->sum('total_amount') ?? 0
                ];
            });
        
        // Orders by status
        $ordersByStatus = [
            'pending' => $orders->where('status', 'pending')->count(),
            'processing' => $orders->where('status', 'processing')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];
        
        // Revenue chart data (last 30 days)
        $revenueChart = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayRevenue = $orders->where('status', 'delivered')
                ->whereBetween('created_at', [
                    now()->subDays($i)->startOfDay(),
                    now()->subDays($i)->endOfDay()
                ])->sum('total_amount');
            $revenueChart[] = [
                'date' => $date,
                'revenue' => $dayRevenue
            ];
        }

        $analytics = [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'avg_order_value' => round($avgOrderValue, 2),
            'conversion_rate' => round($conversionRate, 2) . '%',
            'top_products' => $topProducts,
            'revenue_chart' => $revenueChart,
            'orders_by_status' => $ordersByStatus,
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function getStoreSettings(Request $request)
    {
        $userId = $request->user()->id;
        
        // Get store settings from user settings or create default
        $storeSettings = Setting::where('user_id', $userId)
            ->where('key', 'store_settings')
            ->first();
        
        if ($storeSettings) {
            $settings = json_decode($storeSettings->value, true);
        } else {
            $settings = [
                'store_name' => $request->user()->name . "'s Store",
                'store_description' => 'Welcome to our online store',
                'logo' => null,
                'currency' => 'USD',
                'tax_rate' => 0,
                'shipping_settings' => [
                    'free_shipping_threshold' => 50,
                    'standard_shipping_rate' => 5.99,
                    'express_shipping_rate' => 15.99
                ],
                'payment_methods' => [
                    'stripe' => ['enabled' => false],
                    'paypal' => ['enabled' => false],
                    'razorpay' => ['enabled' => false]
                ],
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function updateStoreSettings(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string',
            'currency' => 'required|string|max:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'logo' => 'nullable|string', // Base64 encoded image
            'shipping_settings' => 'nullable|array',
            'payment_methods' => 'nullable|array',
        ]);

        $userId = $request->user()->id;
        
        // Prepare settings data
        $settingsData = [
            'store_name' => $request->store_name,
            'store_description' => $request->store_description,
            'currency' => $request->currency,
            'tax_rate' => $request->tax_rate,
            'logo' => $request->logo,
            'shipping_settings' => $request->shipping_settings ?? [],
            'payment_methods' => $request->payment_methods ?? [],
            'updated_at' => now()->toISOString(),
        ];

        // Update or create store settings
        Setting::updateOrCreate(
            ['user_id' => $userId, 'key' => 'store_settings'],
            ['value' => json_encode($settingsData)]
        );

        return response()->json([
            'success' => true,
            'message' => 'Store settings updated successfully',
            'data' => $settingsData,
        ]);
    }
}