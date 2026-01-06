<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductReview;
use App\Models\OrderItem;
use App\Models\Order;

class ProductReviewController extends Controller
{
    public function store(Request $request, $productId)
    {
        $customerId = auth()->user()->customer->customer_id;

        // Validate input
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Kiểm tra đã mua sản phẩm chưa
        $hasPurchased = OrderItem::join('orders', 'orders.order_id', '=', 'order_items.order_id')
            ->where('orders.customer_id', $customerId)
            ->where('order_items.product_id', $productId)
            ->whereIn('orders.status', ['paid', 'completed'])
            ->exists();

        if (!$hasPurchased) {
            return response()->json([
                'message' => 'Bạn chỉ có thể đánh giá sản phẩm đã mua'
            ], 403);
        }

        //  Kiểm tra đã review chưa
        $alreadyReviewed = ProductReview::where('product_id', $productId)
            ->where('customer_id', $customerId)
            ->exists();

        if ($alreadyReviewed) {
            return response()->json([
                'message' => 'Bạn đã đánh giá sản phẩm này rồi'
            ], 409);
        }

        // 4️⃣ Lưu review
        $review = ProductReview::create([
            'product_id'  => $productId,
            'customer_id' => $customerId,
            'rating'      => $request->rating,
            'review_text' => $request->review_text,
        ]);

        return response()->json([
            'message' => 'Đánh giá sản phẩm thành công',
            'data' => $review
        ], 201);
    }
}
