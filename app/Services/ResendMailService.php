<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResendMailService
{
    public static function sendOtp($email, $otp)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.resend.com/emails', [
                    'from' => 'MyShoes OTP <onboarding@resend.dev>',
                    'to' => [$email],
                    'subject' => 'Mã OTP xác thực tài khoản MyShoes',
                    'html' => "
                        <div style='font-family: Arial, sans-serif; padding: 20px;'>
                            <h2 style='color: #333;'>Mã OTP của bạn</h2>
                            <p style='font-size: 24px; font-weight: bold; color: #007bff;'>$otp</p>
                            <p style='color: #666;'>Mã có hiệu lực trong <b>5 phút</b>.</p>
                        </div>
                    ",
                ]);

            Log::info('Resend API Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->failed()) {
                throw new \Exception('Resend API error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Send OTP failed: ' . $e->getMessage());
            throw $e;
        }
    }
}