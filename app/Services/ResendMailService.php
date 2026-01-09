<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResendMailService
{
    public static function sendOtp($email, $otp)
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.resend.com/emails', [
                'from' => 'MyShoes OTP <onboarding@resend.dev>',
                'to' => [$email],
                'subject' => 'Mã OTP xác thực tài khoản MyShoes',
                'html' => "
                    <h2>Mã OTP của bạn</h2>
                    <p style='font-size:20px'><b>$otp</b></p>
                    <p>Mã có hiệu lực trong <b>5 phút</b>.</p>
                ",
            ]);

            Log::info('Resend Response:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                // Nếu lỗi 403/429 = rate limit hoặc email không trong whitelist
                $error = $response->json();
                Log::error('Resend Error:', $error);
                
                throw new \Exception(
                    $error['message'] ?? 'Không thể gửi email. Vui lòng thử lại sau.'
                );
            }

            return true;
            
        } catch (\Exception $e) {
            Log::error('Send OTP failed:', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}