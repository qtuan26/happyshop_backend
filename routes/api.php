<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\QuickBuyController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerOrderController;


use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminProductController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Guest routes (chỉ cho người chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);
    Route::get('/chat/{conversationId}', [ChatController::class, 'getConversationMessages']);
    Route::post('/chat/send', [ChatController::class, 'adminSendMessage']);

    // Quản lý khách hàng
    Route::prefix('customers')->group(function () {
        Route::get('/', [AdminCustomerController::class, 'index']); // Danh sách
        Route::get('/statistics', [AdminCustomerController::class, 'statistics']); // Thống kê
        Route::get('/{id}', [AdminCustomerController::class, 'show']); // Chi tiết
        Route::post('/', [AdminCustomerController::class, 'store']); // Thêm mới
        Route::put('/{id}', [AdminCustomerController::class, 'update']); // Cập nhật
        Route::delete('/{id}', [AdminCustomerController::class, 'destroy']); // Xóa
        Route::post('/bulk-delete', [AdminCustomerController::class, 'bulkDelete']); // Xóa nhiều
    });
    // Quản lý đơn hàng
    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index']);        // Danh sách
        Route::get('/{id}', [AdminOrderController::class, 'show']);     // Chi tiết
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus']); // Cập nhật trạng thái
        Route::put('/{id}/cancel', [AdminOrderController::class, 'cancelOrder']);    
    });

    //Quản lý giảm giá
    Route::prefix('coupons')->group(function () {
            Route::get('/', [AdminCouponController::class, 'index']);
            Route::get('/{id}', [AdminCouponController::class, 'show']);
            Route::post('/', [AdminCouponController::class, 'store']);
            Route::put('/{id}', [AdminCouponController::class, 'update']);
            Route::delete('/{id}', [AdminCouponController::class, 'destroy']);
            Route::put('/{id}/toggle', [AdminCouponController::class, 'toggleActive']);
        });

    // Quản lý sản phẩm
    Route::prefix('products')->group(function () {
        Route::get('/', [AdminProductController::class, 'index']);
        Route::get('/statistics', [AdminProductController::class, 'statistics']); // ⬅️ MỚI
        Route::get('/{id}', [AdminProductController::class, 'show']);
        Route::post('/', [AdminProductController::class, 'store']);
        Route::post('/{id}', [AdminProductController::class, 'update']);
        Route::delete('/{id}', [AdminProductController::class, 'destroy']);
        Route::post('/{id}/toggle', [AdminProductController::class, 'toggleActive']);
        
        
        Route::get('/{id}/inventory', [AdminProductController::class, 'getInventory']);
        Route::post('/{id}/inventory', [AdminProductController::class, 'updateInventory']);
        Route::get('/{id}/reviews', [AdminProductController::class, 'getReviews']);
        Route::get('/{id}/sales-history', [AdminProductController::class, 'getSalesHistory']);
    });
    


    
    
    
});

// Customer routes (đã đăng nhập)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function () {
        return auth()->user();
    });
    
    // Customer profile
    Route::get('/customer/profile', [CustomerController::class, 'profile']);
    Route::put('/customer/profile', [CustomerController::class, 'updateProfile']);

    Route::get('/orders', [CustomerOrderController::class, 'index']);
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [CustomerOrderController::class, 'cancel']);
    
    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/item/{id}', [CartController::class, 'update']);
        Route::delete('/item/{id}', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::post('/apply-coupon', [CartController::class, 'applyCoupon']);
        Route::post('/checkout', [CartController::class, 'checkout']);
        Route::post('/checkout/momo/confirm', [CartController::class, 'confirmMomo']);
    });
    
    // Quick Buy
    Route::prefix('quick-buy')->group(function () {
        Route::post('/apply-coupon', [QuickBuyController::class, 'applyCoupon']);
        Route::post('/checkout', [QuickBuyController::class, 'checkout']);
        Route::post('/confirm-momo', [QuickBuyController::class, 'confirmMomo']);
    });
    
    // Reviews
    Route::post('/products/{productId}/reviews', [ProductReviewController::class, 'store']);
    
    // Chat
    Route::prefix('chat')->group(function () {
        Route::get('/conversation', [ChatController::class, 'getOrCreateConversation']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
        Route::get('/{conversationId}/messages', [ChatController::class, 'getNewMessages']);
    });
});

// Public routes (không cần đăng nhập)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/products', [CategoryController::class, 'products']);
Route::get('/categories/{id}/products/filter', [CategoryController::class, 'filterProducts']);
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/products/top-selling', [ProductController::class, 'topSelling']);
Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id');
Route::get('/coupons', [CouponController::class, 'index']);
Route::get('/coupons/{id}', [CouponController::class, 'show']);