<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ProductReview;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;

class AdminProductController extends Controller
{
    /**
     * Khởi tạo Cloudinary SDK
     */
    private function cloudinary(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
        ]);
    }

    /**
     * 📋 Danh sách sản phẩm với thông tin đầy đủ
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Filter theo trạng thái
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter theo category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter theo brand
        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter theo khoảng giá
        if ($request->has('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Search theo tên
        if ($request->has('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderByDesc('created_at')->paginate(10);

        // Thêm thông tin tồn kho và đã bán cho mỗi sản phẩm
        $products->getCollection()->transform(function ($product) {
            // Tổng tồn kho
            $totalStock = Inventory::where('product_id', $product->product_id)
                ->sum('quantity');

            // Số lượng đã bán
            $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->where('order_items.product_id', $product->product_id)
                ->where('orders.status', 'completed')
                ->sum('order_items.quantity');

            // Doanh thu
            $revenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
                ->where('order_items.product_id', $product->product_id)
                ->where('orders.status', 'completed')
                ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

            return [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'url_image' => $product->url_image,
                'base_price' => $product->base_price,
                'color' => $product->color,
                'material' => $product->material,
                'gender' => $product->gender,
                'is_active' => $product->is_active,
                'brand' => [
                    'brand_id' => $product->brand->brand_id ?? null,
                    'brand_name' => $product->brand->brand_name ?? null,
                ],
                'category' => [
                    'category_id' => $product->category->category_id ?? null,
                    'category_name' => $product->category->category_name ?? null,
                ],
                'stock' => [
                    'total_quantity' => $totalStock,
                    'status' => $totalStock > 0 ? 'in_stock' : 'out_of_stock',
                ],
                'reviews' => [
                    'total_reviews' => $product->reviews_count,
                    'average_rating' => $product->reviews_avg_rating ? round($product->reviews_avg_rating, 1) : 0,
                ],
                'sales' => [
                    'total_sold' => $totalSold,
                    'revenue' => $revenue,
                ],
                'created_at' => $product->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * 🔍 Chi tiết sản phẩm với tất cả thông tin
     */
    public function show($id)
    {
        $product = Product::with(['category', 'brand'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        // Lấy chi tiết tồn kho theo size
        $inventory = Inventory::where('product_id', $id)
            ->select('inventory_id', 'size', 'quantity', 'last_updated')
            ->get();

        // Tổng tồn kho
        $totalStock = $inventory->sum('quantity');

        // Thông tin đánh giá
        $reviews = ProductReview::where('product_id', $id)
            ->with('customer')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($review) {
                return [
                    'review_id' => $review->review_id,
                    'rating' => $review->rating,
                    'review_text' => $review->review_text,
                    'customer_name' => $review->customer->full_name ?? 'Khách hàng',
                    'created_at' => $review->created_at,
                ];
            });

        $avgRating = ProductReview::where('product_id', $id)->avg('rating');
        $totalReviews = $reviews->count();

        // Phân bố rating
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = ProductReview::where('product_id', $id)
                ->where('rating', $i)
                ->count();
            $ratingDistribution[$i . '_star'] = $count;
        }

        // Thông tin bán hàng
        $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('order_items.product_id', $id)
            ->where('orders.status', 'completed')
            ->sum('order_items.quantity');
        $revenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('order_items.product_id', $id)
            ->where('orders.status', 'completed')
            ->sum(DB::raw('order_items.quantity * order_items.unit_price'));

        // Sản phẩm liên quan (cùng category)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('product_id', '!=', $id)
            ->where('is_active', true)
            ->select('product_id', 'product_name', 'url_image', 'base_price')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'url_image' => $product->url_image,
                    'description' => $product->description,
                    'base_price' => $product->base_price,
                    'color' => $product->color,
                    'material' => $product->material,
                    'gender' => $product->gender,
                    'date_added' => $product->date_added,
                    'is_active' => $product->is_active,
                    'created_at' => $product->created_at,
                ],
                'brand' => [
                    'brand_id' => $product->brand->brand_id ?? null,
                    'brand_name' => $product->brand->brand_name ?? null,
                    'country' => $product->brand->country ?? null,
                ],
                'category' => [
                    'category_id' => $product->category->category_id ?? null,
                    'category_name' => $product->category->category_name ?? null,
                ],
                'inventory' => [
                    'total_quantity' => $totalStock,
                    'status' => $totalStock > 0 ? 'in_stock' : 'out_of_stock',
                    'sizes' => $inventory,
                ],
                'reviews' => [
                    'total_reviews' => $totalReviews,
                    'average_rating' => $avgRating ? round($avgRating, 1) : 0,
                    'rating_distribution' => $ratingDistribution,
                    'latest_reviews' => $reviews->take(10),
                ],
                'sales' => [
                    'total_sold' => $totalSold,
                    'revenue' => $revenue,
                ],
                'related_products' => $relatedProducts,
            ],
        ]);
    }

    /**
     * 📊 Thống kê tổng quan sản phẩm
     */
    public function statistics()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = Product::where('is_active', false)->count();

        // Sản phẩm hết hàng
        $outOfStockProducts = Product::whereDoesntHave('inventory', function ($query) {
            $query->where('quantity', '>', 0);
        })->count();

        // Sản phẩm sắp hết hàng (tổng < 10)
        $lowStockProducts = Product::whereHas('inventory', function ($query) {
            $query->select('product_id')
                ->groupBy('product_id')
                ->havingRaw('SUM(quantity) < 10')
                ->havingRaw('SUM(quantity) > 0');
        })->count();

        // Top sản phẩm bán chạy
        $topSellingProducts = DB::table('products')
        ->join('order_items', 'products.product_id', '=', 'order_items.product_id')
        ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
        ->where('orders.status', 'completed')
        ->select(
            'products.product_id',
            'products.product_name',
            'products.url_image',
            'products.base_price'
        )
        ->selectRaw('SUM(order_items.quantity) as total_sold')
        ->groupBy(
            'products.product_id',
            'products.product_name',
            'products.url_image',
            'products.base_price'
        )
        ->orderByDesc('total_sold')
        ->limit(5)
        ->get();

        // Sản phẩm đánh giá cao
        $topRatedProducts = DB::table('products')
            ->join('product_reviews', 'products.product_id', '=', 'product_reviews.product_id')
            ->select('products.product_id', 'products.product_name', 'products.url_image')
            ->selectRaw('AVG(product_reviews.rating) as avg_rating, COUNT(product_reviews.review_id) as review_count')
            ->groupBy('products.product_id', 'products.product_name', 'products.url_image')
            ->having('review_count', '>=', 5)
            ->orderByDesc('avg_rating')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'url_image' => $product->url_image,
                    'average_rating' => round($product->avg_rating, 1),
                    'review_count' => $product->review_count,
                ];
            });

        // Thống kê theo category
        $categoryStats = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->select('categories.category_name', DB::raw('COUNT(*) as product_count'))
            ->groupBy('categories.category_id', 'categories.category_name')
            ->get();

        // Thống kê theo brand
        $brandStats = DB::table('products')
            ->join('brands', 'products.brand_id', '=', 'brands.brand_id')
            ->select('brands.brand_name', DB::raw('COUNT(*) as product_count'))
            ->groupBy('brands.brand_id', 'brands.brand_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'inactive_products' => $inactiveProducts,
                    'out_of_stock_products' => $outOfStockProducts,
                    'low_stock_products' => $lowStockProducts,
                ],
                'top_selling_products' => $topSellingProducts,
                'top_rated_products' => $topRatedProducts,
                'category_stats' => $categoryStats,
                'brand_stats' => $brandStats,
            ],
        ]);
    }

    /**
     * 📦 Lấy chi tiết tồn kho theo size
     */
    public function getInventory($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $inventory = Inventory::where('product_id', $id)
            ->orderBy('size')
            ->get();

        $totalStock = $inventory->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => [
                'product_id' => $product->product_id,
                'product_name' => $product->product_name,
                'total_stock' => $totalStock,
                'inventory' => $inventory,
            ],
        ]);
    }

    /**
     * 📦 Cập nhật tồn kho
     */
    public function updateInventory(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $validated = $request->validate([
            'size' => 'required|string|max:10',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::updateOrCreate(
            [
                'product_id' => $id,
                'size' => $validated['size'],
            ],
            [
                'quantity' => $validated['quantity'],
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tồn kho thành công',
            'data' => $inventory,
        ]);
    }

    /**
     * ⭐ Lấy danh sách đánh giá
     */
    public function getReviews($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $reviews = ProductReview::where('product_id', $id)
            ->with('customer')
            ->orderByDesc('created_at')
            ->paginate(20);

        $avgRating = ProductReview::where('product_id', $id)->avg('rating');

        // Phân bố rating
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = ProductReview::where('product_id', $id)
                ->where('rating', $i)
                ->count();
            $percentage = $reviews->total() > 0 ? round(($count / $reviews->total()) * 100, 1) : 0;
            $ratingDistribution[] = [
                'rating' => $i,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'product_name' => $product->product_name,
                'summary' => [
                    'total_reviews' => $reviews->total(),
                    'average_rating' => $avgRating ? round($avgRating, 1) : 0,
                    'rating_distribution' => $ratingDistribution,
                ],
                'reviews' => $reviews->map(function ($review) {
                    return [
                        'review_id' => $review->review_id,
                        'rating' => $review->rating,
                        'review_text' => $review->review_text,
                        'customer' => [
                            'customer_id' => $review->customer->customer_id ?? null,
                            'full_name' => $review->customer->full_name ?? 'Khách hàng',
                            'email' => $review->customer->email ?? null,
                        ],
                        'created_at' => $review->created_at,
                    ];
                }),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
            ],
        ]);
    }

    /**
     * 📈 Lịch sử bán hàng
     */
    public function getSalesHistory($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $salesHistory = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->where('order_items.product_id', $id)
            ->where('orders.status', 'completed')
            ->with(['order.customer'])
            ->orderByDesc('order_items.created_at')
            ->paginate(20);

        $totalSold = OrderItem::where('product_id', $id)->sum('quantity');
        $totalRevenue = OrderItem::where('product_id', $id)
            ->sum(DB::raw('quantity * unit_price'));

        // Thống kê theo tháng (6 tháng gần nhất)
        $monthlySales = OrderItem::where('product_id', $id)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total_quantity, SUM(quantity * unit_price) as total_revenue')
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product_name' => $product->product_name,
                'summary' => [
                    'total_sold' => $totalSold,
                    'total_revenue' => $totalRevenue,
                ],
                'monthly_sales' => $monthlySales,
                'sales_history' => $salesHistory->map(function ($item) {
                    return [
                        'order_id' => $item->order_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->quantity * $item->unit_price,
                        'size' => $item->size,
                        'customer' => [
                            'full_name' => $item->order->customer->full_name ?? 'Khách hàng',
                            'email' => $item->order->customer->email ?? null,
                        ],
                        'order_date' => $item->created_at,
                    ];
                }),
                'pagination' => [
                    'current_page' => $salesHistory->currentPage(),
                    'last_page' => $salesHistory->lastPage(),
                    'per_page' => $salesHistory->perPage(),
                    'total' => $salesHistory->total(),
                ],
            ],
        ]);
    }

    /**
     * ➕ Tạo sản phẩm + upload ảnh
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'brand_id'    => 'required|exists:brands,brand_id',
        'category_id' => 'required|exists:categories,category_id',
        'product_name'=> 'required|string|max:255',
        'description' => 'nullable|string',
        'base_price'  => 'required|numeric|min:0',
        'color'       => 'nullable|string|max:50',
        'material'    => 'nullable|string|max:50',
        'gender'      => 'nullable|string|max:20',
        'image'       => 'required|image|max:2048',
        
        // Inventory validation
        'inventory'   => 'required|array|min:1',
        'inventory.*.size' => 'required|string|max:10',
        'inventory.*.quantity' => 'required|integer|min:0',
    ], [
        'inventory.required' => 'Vui lòng thêm ít nhất một size với số lượng',
        'inventory.*.size.required' => 'Size không được để trống',
        'inventory.*.quantity.required' => 'Số lượng không được để trống',
        'inventory.*.quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 0',
    ]);

    return DB::transaction(function () use ($validated, $request) {

        // Upload ảnh
        $upload = $this->cloudinary()
            ->uploadApi()
            ->upload(
                $request->file('image')->getRealPath(),
                ['folder' => 'products']
            );

        // Tạo sản phẩm
        $product = Product::create([
            'brand_id'         => $validated['brand_id'],
            'category_id'      => $validated['category_id'],
            'product_name'     => $validated['product_name'],
            'description'      => $validated['description'] ?? null,
            'base_price'       => $validated['base_price'],
            'color'            => $validated['color'] ?? null,
            'material'         => $validated['material'] ?? null,
            'gender'           => $validated['gender'] ?? null,
            'url_image'        => $upload['secure_url'],
            'public_url_image' => $upload['public_id'],
            'date_added'       => now(),
            'is_active'        => true,
        ]);

        // Tạo inventory cho từng size
        foreach ($validated['inventory'] as $item) {
            Inventory::create([
                'product_id' => $product->product_id,
                'size' => $item['size'],
                'quantity' => $item['quantity'],
            ]);
        }

        // Load lại product với inventory
        $product->load('inventory');

        return response()->json([
            'success' => true,
            'message' => 'Tạo sản phẩm thành công',
            'data' => $product,
        ], 201);
    });
    }

    /**
     * ✏️ Cập nhật sản phẩm (có thể đổi ảnh)
     */
    public function update(Request $request, $id)
    {
         $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại',
        ], 404);
    }

    $validated = $request->validate([
        'brand_id'    => 'required|exists:brands,brand_id',
        'category_id' => 'required|exists:categories,category_id',
        'product_name'=> 'required|string|max:255',
        'description' => 'nullable|string',
        'base_price'  => 'required|numeric|min:0',
        'color'       => 'nullable|string|max:50',
        'material'    => 'nullable|string|max:50',
        'gender'      => 'nullable|string|max:20',
        'image'       => 'nullable|image|max:2048',
    ]);

    return DB::transaction(function () use ($product, $validated, $request) {

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($product->public_url_image) {
                $this->cloudinary()
                    ->uploadApi()
                    ->destroy($product->public_url_image);
            }

            // Upload ảnh mới
            $upload = $this->cloudinary()
                ->uploadApi()
                ->upload(
                    $request->file('image')->getRealPath(),
                    ['folder' => 'products']
                );

            $validated['url_image']        = $upload['secure_url'];
            $validated['public_url_image'] = $upload['public_id'];
        }

        // Update product info (NO inventory update here)
        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công',
            'data' => $product,
        ]);
    });
    }

    /**
     * ❌ Xóa sản phẩm
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        if ($product->public_url_image) {
            $this->cloudinary()
                ->uploadApi()
                ->destroy($product->public_url_image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công',
        ]);
    }

    /**
     * 🔁 Bật / Tắt sản phẩm
     */
    public function toggleActive($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái sản phẩm thành công',
            'is_active' => $product->is_active,
        ]);
    }
}