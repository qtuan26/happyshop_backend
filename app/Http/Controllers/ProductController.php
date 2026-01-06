<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;

class ProductController extends Controller
{
    // GET /api/products/{id}
    // public function show($id)
    // {
    //     $product = Product::where('product_id', $id)
    //         ->where('is_active', 1)
    //         ->select(
    //             'product_id',
    //             'brand_id',
    //             'category_id',
    //             'product_name',
    //             'url_image',
    //             'public_url_image',
    //             'description',
    //             'base_price',
    //             'color',
    //             'material',
    //             'gender',
    //             'date_added'
    //         )
    //         ->first();

    //     if (!$product) {
    //         return response()->json([
    //             'message' => 'Product not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'data' => $product
    //     ]);
    // }

     // GET /api/products/{id}
    public function show($id)
    {
        $product = Product::with([
        'inventory' => function ($q) {
            $q->select('inventory_id', 'product_id', 'size', 'quantity');
        },
        'reviews.customer' => function ($q) {
            $q->select('customer_id', 'full_name');
        }
    ])
    ->where('product_id', $id)
    ->where('is_active', 1)
    ->first();

    if (!$product) {
        return response()->json([
            'message' => 'Product not found'
        ], 404);
    }

    // Format reviews để có tên khách hàng
    $reviews = $product->reviews->map(function ($review) {
        return [
            'review_id'   => $review->review_id,
            'rating'      => $review->rating,
            'review_text'=> $review->review_text,
            'created_at' => $review->created_at,
            'customer'   => [
                'customer_id' => $review->customer?->customer_id,
                'full_name'   => $review->customer?->full_name,
            ]
        ];
    });

    return response()->json([
        'product' => [
            'product_id'   => $product->product_id,
            'brand_id'     => $product->brand_id,
            'category_id'  => $product->category_id,
            'product_name' => $product->product_name,
            'url_image'    => $product->url_image,
            'base_price'   => $product->base_price,
            'description' => $product->description,

            'inventory'    => $product->inventory,
            'reviews'      => $reviews,

            'avg_rating'   => round($product->reviews->avg('rating'), 1),
            'review_count' => $product->reviews->count(),
            'total_stock'  => $product->inventory->sum('quantity'),
        ]
    ]);
    }
    // GET /api/products/search?q=abc
    public function search(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword) {
            return response()->json(['data' => []]);
        }

        $products = Product::where('is_active', true)
            ->where('product_name', 'LIKE', '%' . $keyword . '%')
            ->select('product_id', 'category_id', 'product_name','base_price','url_image')
            ->limit(10)
            ->get();

        return response()->json(['data' => $products]);
    }

    
    // Lấy top 6 sản phẩm bán chạy nhất
     
    // GET /api/products/top-selling
    public function topSelling()
    {
        $topProducts = OrderItem::join('orders', 'orders.order_id', '=', 'order_items.order_id')
            ->join('products', 'products.product_id', '=', 'order_items.product_id')
            ->whereIn('orders.status', ['paid', 'completed'])
            ->where('products.is_active', 1) 
            ->groupBy(
                'products.product_id',
                'products.category_id',
                'products.product_name',
                'products.url_image',
                'products.base_price'
            )
            ->selectRaw('
                products.product_id,
                products.category_id,
                products.product_name,
                products.url_image,
                products.base_price,
                SUM(order_items.quantity) as total_sold
            ')
            ->orderByDesc('total_sold')
            ->limit(6)
            ->get();

        return response()->json(['data' => $topProducts]);
    }

}
