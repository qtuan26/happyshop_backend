<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ResendMailService
{
    public static function sendOtp($email, $otp)
    {
        try {
            // Tăng timeout lên 60 giây
            Config::set('mail.mailers.smtp.timeout', 60);
            
            Mail::html("
                <div style='font-family: Arial, sans-serif; padding: 20px;'>
                    <h2 style='color: #333;'>Mã OTP của bạn</h2>
                    <p style='font-size: 24px; font-weight: bold; color: #007bff;'>$otp</p>
                    <p style='color: #666;'>Mã có hiệu lực trong <b>5 phút</b>.</p>
                </div>
            ", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Mã OTP xác thực tài khoản MyShoes');
            });

            Log::info('OTP sent to: ' . $email);
            
        } catch (\Exception $e) {
            Log::error('Send OTP failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Không thể gửi email. Vui lòng thử lại.');
        }
    }
}