<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * XEM THÔNG TIN CÁ NHÂN
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Lấy thông tin cá nhân thành công',
            'data' => $customer
        ]);
    }

    /**
     * CẬP NHẬT THÔNG TIN CÁ NHÂN
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'data' => $customer
        ]);
    }
}
