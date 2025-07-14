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
        $analytics = [
            'total_revenue' => 0,
            'total_orders' => 0,
            'avg_order_value' => 0,
            'conversion_rate' => '0%',
            'top_products' => [],
            'revenue_chart' => [],
            'orders_by_status' => [
                'pending' => 0,
                'processing' => 0,
                'shipped' => 0,
                'delivered' => 0,
                'cancelled' => 0,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
        ]);
    }

    public function getStoreSettings(Request $request)
    {
        // TODO: Get store settings for the user
        $settings = [
            'store_name' => '',
            'store_description' => '',
            'logo' => null,
            'currency' => 'USD',
            'tax_rate' => 0,
            'shipping_settings' => [],
            'payment_methods' => [],
        ];

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
        ]);

        // TODO: Update store settings for the user

        return response()->json([
            'success' => true,
            'message' => 'Store settings updated successfully',
        ]);
    }
}