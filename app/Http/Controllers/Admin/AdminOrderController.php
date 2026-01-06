<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    /**
     * 📋 Danh sách đơn hàng
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer.user']);

        // Filter theo status (optional)
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Sort mới nhất
        $orders = $query->orderByDesc('created_at')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * 🔍 Chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with([
            'customer.user',
            'items.product',
            'coupons.coupon'
        ])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * 🔄 Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        $invalidTransitions = [
            'completed' => [ 'awaiting_confirmation'],
            'cancelled' => ['awaiting_confirmation'],
            
        ];

        $order = Order::find($id);
        $currentStatus = $order->status;
        $newStatus = $request->status;

        if (
            isset($invalidTransitions[$currentStatus]) &&
            in_array($newStatus, $invalidTransitions[$currentStatus])
        ) {
            return response()->json([
                'success' => false,
                'message' => "Không thể chuyển trạng thái từ $currentStatus sang $newStatus"
            ], 400);
        }

        // $request->validate([
        //     'status' => 'required|string|in:pending,paid,awaiting_confirmation,shipping,cancelled,completed'
        // ]);

        // $order = Order::find($id);

        // if (!$order) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Không tìm thấy đơn hàng'
        //     ], 404);
        // }

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $order
        ]);
    }

    /**
     * ❌ Hủy đơn hàng
     */
    public function cancelOrder(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {

            $order = Order::with(['items', 'coupons.coupon'])
                ->lockForUpdate()
                ->findOrFail($id);

            //  Không cho hủy nếu đã giao
            if (in_array($order->status, ['shipping', 'completed'])) {
                return response()->json([
                    'message' => 'Không thể hủy đơn hàng đã giao hoặc đang giao'
                ], 400);
            }

            //  Đã hủy rồi
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

                    // bật lại nếu trước đó bị disable
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
                'order' => $order->fresh()->load('items.product', 'coupons.coupon')
            ]);
        });
    }
}
