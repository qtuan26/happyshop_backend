<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminCustomerController extends Controller
{
    /**
     * Lấy danh sách tất cả khách hàng (có phân trang)
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'customer_id');
            $sortOrder = $request->input('sort_order', 'desc');

            $query = Customer::with('user:id,email');

            // Tìm kiếm
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                });
            }

            // Sắp xếp
            $query->orderBy($sortBy, $sortOrder);

            $customers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách khách hàng thành công',
                'data' => $customers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem chi tiết một khách hàng
     */
    public function show($id)
    {
        try {
            $customer = Customer::with(['user:id,email', 'orders'])->find($id);
            

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khách hàng'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lấy thông tin khách hàng thành công',
                'data' => $customer
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm khách hàng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'full_name' => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string',
            'city'      => 'nullable|string',
            'state'     => 'nullable|string',
            'zip_code'  => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Tạo user
            $user = User::create([
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'customer',
            ]);

            // 2️⃣ Tạo customer
            $customer = Customer::create([
                'user_id' => $user->id,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip_code' => $request->zip_code,
                'registration_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'customer' => $customer
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin khách hàng
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($customer->user_id)
            ],
            'password' => 'sometimes|min:6',
            'full_name' => 'sometimes|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'registration_date' => 'sometimes|date'
        ], [
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'full_name.max' => 'Họ tên không được vượt quá 100 ký tự'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Cập nhật user nếu có email hoặc password
            if ($request->has('email') || $request->has('password')) {
                $userUpdate = [];
                if ($request->has('email')) {
                    $userUpdate['email'] = $request->email;
                }
                if ($request->has('password')) {
                    $userUpdate['password'] = Hash::make($request->password);
                }
                $customer->user->update($userUpdate);
            }

            // Cập nhật customer
            $customerData = $request->only([
                'full_name',
                'phone',
                'address',
                'city',
                'state',
                'zip_code',
                'registration_date'
            ]);

            $customer->update(array_filter($customerData, function($value) {
                return $value !== null;
            }));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khách hàng thành công',
                'data' => $customer->fresh()->load('user:id,email')
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa khách hàng
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $customer = Customer::with('user')->find($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khách hàng'
                ], 404);
            }

            // Xóa user trước (customer sẽ tự xóa hoặc ngược lại đều được)
            if ($customer->user) {
                $customer->user->delete();
            }

            // Xóa customer
            $customer->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa khách hàng thành công'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa nhiều khách hàng
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_ids' => 'required|array',
            'customer_ids.*' => 'exists:customers,customer_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $customers = Customer::with('user')
                ->whereIn('customer_id', $request->customer_ids)
                ->get();

            foreach ($customers as $customer) {
                if ($customer->user) {
                    $customer->user->delete();
                }
                $customer->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa nhiều khách hàng thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê khách hàng
     */
    public function statistics()
    {
        try {
            $totalCustomers = Customer::count();
            $newCustomersThisMonth = Customer::whereMonth('registration_date', now()->month)
                                             ->whereYear('registration_date', now()->year)
                                             ->count();
            $customersWithOrders = Customer::has('orders')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_customers' => $totalCustomers,
                    'new_customers_this_month' => $newCustomersThisMonth,
                    'customers_with_orders' => $customersWithOrders
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}