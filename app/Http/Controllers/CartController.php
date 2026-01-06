<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ShoppingCart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderCoupon;
use App\Models\Inventory;

class CartController extends Controller
{
    /* ======================= GET CART ======================= */
    public function index(Request $request)
    {
        $customer = $request->user()->customer;
        
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $cart = ShoppingCart::firstOrCreate([
            'customer_id' => $customer->customer_id
        ]);

        $cart->load('items.product');

        return response()->json([
            'cart_id' => $cart->cart_id,
            'items' => $cart->items->map(fn ($item) => [
                'cart_item_id' => $item->cart_item_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'url_image' => $item->product->url_image,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'price' => $item->product->base_price,
                'subtotal' => $item->quantity * $item->product->base_price,
            ])
        ]);
    }

    /* ======================= ADD TO CART ======================= */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'size' => 'required|string|max:10',
            'quantity' => 'required|integer|min:1',
        ]);

        $customer = $request->user()->customer;
        
        $cart = ShoppingCart::firstOrCreate([
            'customer_id' => $customer->customer_id
        ]);

        $item = CartItem::where('cart_id', $cart->cart_id)
            ->where('product_id', $request->product_id)
            ->where('size', $request->size)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'product_id' => $request->product_id,
                'size' => $request->size,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json(['message' => 'Added to cart']);
    }

    /* ======================= UPDATE CART ITEM ======================= */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        CartItem::findOrFail($id)->update([
            'quantity' => $request->quantity
        ]);

        return response()->json(['message' => 'Cart item updated']);
    }

    /* ======================= REMOVE CART ITEM ======================= */
    public function remove($id)
    {
        CartItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Item removed']);
    }

    /* ======================= CLEAR CART ======================= */
    public function clear(Request $request)
    {
        $customer = $request->user()->customer;
        $cart = ShoppingCart::where('customer_id', $customer->customer_id)->first();
        
        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared']);
    }

    /* ======================= APPLY COUPON ======================= */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $customer = $request->user()->customer;
        $cart = ShoppingCart::with('items.product')
            ->firstOrCreate(['customer_id' => $customer->customer_id]);

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        $coupon = Coupon::where('coupon_code', $request->coupon_code)
            ->where('is_active', 1)
            ->first();

        if (!$coupon) {
            return response()->json(['message' => 'Coupon không tồn tại'], 400);
        }

        $now = now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return response()->json(['message' => 'Coupon chưa bắt đầu'], 400);
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return response()->json(['message' => 'Coupon đã hết hạn'], 400);
        }

        if ($coupon->usage_limit !== null && $coupon->usage_limit <= 0) {
            return response()->json(['message' => 'Coupon đã hết lượt sử dụng'], 400);
        }

        $subtotal = $cart->items->sum(
            fn ($item) => $item->quantity * $item->product->base_price
        );

        if ($coupon->min_purchase_amount !== null && $subtotal < $coupon->min_purchase_amount) {
            return response()->json([
                'error' => 'MIN_ORDER_NOT_MET',
                'message' => "Đơn hàng tối thiểu \${$coupon->min_purchase_amount}"
            ], 422);
        }

        $discount = 0;
        if ($coupon->discount_type === 'percentage') {
            $discount = $subtotal * ($coupon->discount_value / 100);
            if ($coupon->max_discount_amount) {
                $discount = min($discount, $coupon->max_discount_amount);
            }
        }

        if ($coupon->discount_type === 'fixed_amount') {
            $discount = $coupon->discount_value;
        }

        return response()->json([
            'message' => 'Áp dụng coupon thành công',
            'data' => [
                'coupon_code' => $coupon->coupon_code,
                'discount' => round($discount, 2),
                'discount_type' => $coupon->discount_type
            ]
        ]);
    }

    /* ======================= CHECKOUT ======================= */
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:COD,MOMO',
            'coupon_code' => 'nullable|string'
        ]);

        $customer = $request->user()->customer;
        $cart = ShoppingCart::with('items.product')
            ->where('customer_id', $customer->customer_id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Giỏ hàng trống'], 400);
        }

        return DB::transaction(function () use ($request, $cart, $customer) {
            /* 1️⃣ SUBTOTAL */
            $subtotal = $cart->items->sum(
                fn ($item) => $item->quantity * $item->product->base_price
            );

            /* 2️⃣ SHIPPING */
            $shippingFee = 20;

            /* 3️⃣ COUPON */
            $discountAmount = 0;
            $appliedCoupon = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('coupon_code', $request->coupon_code)
                    ->where('is_active', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$coupon || $coupon->usage_limit <= 0) {
                    throw new \Exception('Coupon không hợp lệ');
                }

                if ($subtotal < $coupon->min_purchase_amount) {
                    throw new \Exception('Không đủ điều kiện áp dụng coupon');
                }

                if ($coupon->discount_type === 'percentage') {
                    $discountAmount = $subtotal * ($coupon->discount_value / 100);
                    if ($coupon->max_discount_amount) {
                        $discountAmount = min($discountAmount, $coupon->max_discount_amount);
                    }
                }

                if ($coupon->discount_type === 'fixed_amount') {
                    $discountAmount = $coupon->discount_value;
                }

                $coupon->decrement('usage_limit');
                if ($coupon->usage_limit <= 0) {
                    $coupon->update(['is_active' => 0]);
                }

                $appliedCoupon = $coupon;
            }

            /* 4️⃣ TOTAL */
            $total = $subtotal + $shippingFee - $discountAmount;

            /* 5️⃣ ORDER STATUS */
            $orderStatus = $request->payment_method === 'MOMO' ? 'pending' : 'paid';

            /* 6️⃣ CREATE ORDER */
            $order = Order::create([
                'customer_id' => $customer->customer_id,
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'status' => $orderStatus,
            ]);

            /* 7️⃣ ORDER ITEMS + INVENTORY (COD only) */
            foreach ($cart->items as $item) {
                // COD → trừ kho ngay
                if ($request->payment_method === 'COD') {
                    $inventory = Inventory::where('product_id', $item->product_id)
                        ->where('size', $item->size)
                        ->lockForUpdate()
                        ->first();

                    if (!$inventory || $inventory->quantity < $item->quantity) {
                        throw new \Exception("Không đủ tồn kho cho sản phẩm {$item->product->product_name} size {$item->size}");
                    }

                    $inventory->decrement('quantity', $item->quantity);
                }

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product_id,
                    'size' => $item->size,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->base_price,
                    'subtotal' => $item->quantity * $item->product->base_price,
                ]);
            }

            /* 8️⃣ ORDER COUPON */
            if ($appliedCoupon) {
                OrderCoupon::create([
                    'order_id' => $order->order_id,
                    'coupon_id' => $appliedCoupon->coupon_id,
                    'discount_applied' => $discountAmount,
                ]);
            }

            /* 9️⃣ CLEAR CART */
            $cart->items()->delete();

            /* 🔟 RESPONSE */
            if ($request->payment_method === 'MOMO') {
                return response()->json([
                    'message' => 'Đơn hàng đã tạo, vui lòng thanh toán qua MOMO',
                    'order' => [
                        'order_id' => $order->order_id,
                        'total_amount' => $total,
                        'status' => 'pending',
                        'payment_method' => 'MOMO'
                    ],
                    'qr_code' => [
                        'order_id' => $order->order_id,
                        'amount' => $total,
                        'method' => 'MOMO',
                        'qr_string' => "MOMO|ORDER_{$order->order_id}|{$total}|VND"
                    ]
                ]);
            }

            // COD response
            return response()->json([
                'message' => 'Đặt hàng thành công (COD)',
                'order' => $order->load('items.product', 'coupons.coupon')
            ]);
        });
    }

    /* ======================= CONFIRM MOMO ======================= */
    public function confirmMomo(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id'
        ]);

        return DB::transaction(function () use ($request) {
            $order = Order::with('items.product')
                ->where('order_id', $request->order_id)
                ->lockForUpdate()
                ->first();

            if ($order->status !== 'pending') {
                return response()->json([
                    'message' => 'Đơn hàng không ở trạng thái chờ thanh toán'
                ], 400);
            }

            // MOMO → trừ kho SAU khi thanh toán
            foreach ($order->items as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->quantity < $item->quantity) {
                    throw new \Exception("Không đủ tồn kho cho sản phẩm {$item->product->product_name} size {$item->size}");
                }

                $inventory->decrement('quantity', $item->quantity);
            }

            $order->update(['status' => 'paid']);

            return response()->json([
                'message' => 'Thanh toán MOMO thành công',
                'order' => $order->load('items.product', 'coupons.coupon')
            ]);
        });
    }
}