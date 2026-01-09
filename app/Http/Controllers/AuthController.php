<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Services\ResendMailService;

class AuthController extends Controller
{
    // Đăng ký
     // ===== GỬI OTP =====
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|min:2',
        ]);

        try {
            // Tạo OTP
            $otp = sprintf("%06d", mt_rand(100000, 999999));

            // Lưu cache 5 phút
            Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

            // Gửi OTP bằng Resend API
            ResendMailService::sendOtp($request->email, $otp);

            return response()->json([
                'message' => 'Mã OTP đã được gửi đến email của bạn',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể gửi OTP',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    // ===== XÁC THỰC VÀ ĐĂNG KÝ =====
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|min:2',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|string|size:6',
        ]);

        // Kiểm tra OTP
        $savedOtp = Cache::get('otp_' . $request->email);
        
        if (!$savedOtp) {
            return response()->json([
                'message' => 'Mã OTP đã hết hạn'
            ], 400);
        }
        
        if ($savedOtp !== $request->otp) {
            return response()->json([
                'message' => 'Mã OTP không đúng'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Tạo user
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
            ]);
            
            // Tạo customer
            Customer::create([
                'user_id' => $user->id,
                'full_name' => $request->name,
                'phone' => $request->phone,
                'registration_date' => now(),
            ]);
            
            // Xóa OTP sau khi dùng
            Cache::forget('otp_' . $request->email);
            
            // Tạo token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            DB::commit();
            
            return response()->json([
                'message' => 'Đăng ký thành công',
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Đăng ký thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // ===== ĐĂNG NHẬP =====
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'data' => $user,
            'token' => $token
        ]);
    }

    // // ===== ĐĂNG XUẤT =====
    // public function logout()
    // {
    //     auth()->user()->tokens()->delete();

    //     return response()->json([
    //         'message' => 'Đăng xuất thành công'
    //     ]);
    // }
}
