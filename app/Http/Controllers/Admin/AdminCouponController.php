<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;

class AdminCouponController extends Controller
{
    /**
     * Khởi tạo Cloudinary SDK (v2.14.0)
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
     * 📋 Danh sách coupon
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        return response()->json([
            'success' => true,
            'data'    => $query->orderByDesc('created_at')->paginate(10),
        ]);
    }

    /**
     * 🔍 Chi tiết coupon
     */
    public function show($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon không tồn tại',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $coupon,
        ]);
    }

    /**
     * ➕ Tạo coupon + upload ảnh Cloudinary
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'coupon_code'          => 'required|string|max:50|unique:coupons,coupon_code',
            'title'                => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'discount_type'        => 'required|in:percentage,fixed_amount',
            'discount_value'       => 'required|numeric|min:0',
            'max_discount_amount'  => 'nullable|numeric|min:0',
            'min_purchase_amount'  => 'nullable|numeric|min:0',
            'usage_limit'          => 'nullable|integer|min:1',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'image'                => 'required|image|max:2048',
        ]);

        return DB::transaction(function () use ($validated, $request) {

            // Upload ảnh
            $upload = $this->cloudinary()
                ->uploadApi()
                ->upload(
                    $request->file('image')->getRealPath(),
                    ['folder' => 'coupons']
                );

            $coupon = Coupon::create([
                'coupon_code'         => $validated['coupon_code'],
                'title'               => $validated['title'] ?? null,
                'description'         => $validated['description'] ?? null,
                'discount_type'       => $validated['discount_type'],
                'discount_value'      => $validated['discount_value'],
                'max_discount_amount' => $validated['max_discount_amount'] ?? null,
                'min_purchase_amount' => $validated['min_purchase_amount'] ?? null,
                'usage_limit'         => $validated['usage_limit'] ?? null,
                'start_date'          => $validated['start_date'],
                'end_date'            => $validated['end_date'] ?? null,
                'url_image'           => $upload['secure_url'],
                'public_url_image'    => $upload['public_id'],
                'is_active'           => true,
            ]);

            return response()->json([
                'message' => 'Tạo coupon thành công',
                'data'    => $coupon,
            ], 201);
        });
    }

    /**
     * ✏️ Cập nhật coupon (có thể đổi ảnh)
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon không tồn tại',
            ], 404);
        }

        $validated = $request->validate([
            'title'                => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'discount_type'        => 'required|in:percentage,fixed_amount',
            'discount_value'       => 'required|numeric|min:0',
            'max_discount_amount'  => 'nullable|numeric|min:0',
            'min_purchase_amount'  => 'nullable|numeric|min:0',
            'usage_limit'          => 'nullable|integer|min:1',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'image'                => 'nullable|image|max:2048',
        ]);

        return DB::transaction(function () use ($coupon, $validated, $request) {

            if ($request->hasFile('image')) {

                // Xóa ảnh cũ
                $this->cloudinary()
                    ->uploadApi()
                    ->destroy($coupon->public_url_image);

                // Upload ảnh mới
                $upload = $this->cloudinary()
                    ->uploadApi()
                    ->upload(
                        $request->file('image')->getRealPath(),
                        ['folder' => 'coupons']
                    );

                $coupon->url_image        = $upload['secure_url'];
                $coupon->public_url_image = $upload['public_id'];
            }

            $coupon->update($validated);

            return response()->json([
                'message' => 'Cập nhật coupon thành công',
                'data'    => $coupon,
            ]);
        });
    }

    /**
     * ❌ Xóa coupon + xóa ảnh Cloudinary
     */
    public function destroy($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon không tồn tại',
            ], 404);
        }

        $this->cloudinary()
            ->uploadApi()
            ->destroy($coupon->public_url_image);

        $coupon->delete();

        return response()->json([
            'message' => 'Xóa coupon thành công',
        ]);
    }

    /**
     * 🔁 Bật / Tắt coupon
     */
    public function toggleActive($id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon không tồn tại',
            ], 404);
        }

        $coupon->update([
            'is_active' => !$coupon->is_active,
        ]);

        return response()->json([
            'message'   => 'Cập nhật trạng thái coupon thành công',
            'is_active' => $coupon->is_active,
        ]);
    }
}
