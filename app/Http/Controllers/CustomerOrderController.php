<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with([
                'items.product',
                'coupons.coupon'
            ])
            ->whereHas('customer', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('created_at')
            ->get(); // hoặc ->get()

        return response()->json([
            'message' => 'Lấy danh sách đơn hàng thành công',
            'data' => $orders
        ]);
    }
     public function show(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::with([
            'items.product',
            'coupons.coupon'
        ])
        ->where('order_id', $id)
        ->whereHas('customer', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy chi tiết đơn hàng thành công',
            'data' => $order
        ]);
    }

    //huy don
    public function cancel(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $user = $request->user();

            $order = Order::with(['items', 'coupons.coupon'])
                ->where('order_id', $id)
                ->whereHas('customer', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->lockForUpdate()
                ->first();

            if (!$order) {
                return response()->json([
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // ❌ Không cho hủy nếu đã giao / hoàn thành
            if (in_array($order->status, ['shipping', 'completed'])) {
                return response()->json([
                    'message' => 'Đơn hàng đang giao hoặc đã hoàn thành, không thể hủy'
                ], 400);
            }

            // ❌ Đã hủy rồi
            if ($order->status === 'cancelled') {
                return response()->json([
                    'message' => 'Đơn hàng đã bị hủy trước đó'
                ], 400);
            }

            /* ================== 1️⃣ HOÀN KHO ================== */
            foreach ($order->items as $item) {
                $inventory = Inventory::where('product_id', $item->product_id)
                    ->where('size', $item->size)
                    ->lockForUpdate()
                    ->first();

                if ($inventory) {
                    $inventory->increment('quantity', $item->quantity);
                }
            }

            /* ================== 2️⃣ HOÀN COUPON ================== */
            foreach ($order->coupons as $orderCoupon) {
                $coupon = $orderCoupon->coupon;

                if ($coupon) {
                    $coupon->increment('usage_limit');

                    if ($coupon->usage_limit > 0 && $coupon->is_active == 0) {
                        $coupon->update(['is_active' => 1]);
                    }
                }
            }

            /* ================== 3️⃣ UPDATE STATUS ================== */
            $order->update([
                'status' => 'cancelled'
            ]);

            return response()->json([
                'message' => 'Hủy đơn hàng thành công',
                'data' => $order->fresh()->load('items.product', 'coupons.coupon')
            ]);
        });
    }
}
