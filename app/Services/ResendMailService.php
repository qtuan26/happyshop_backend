<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ResendMailService
{
    public static function sendOtp($email, $otp)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.resend.com/emails', [
            'from' => env('MAIL_FROM_NAME') . ' <' . env('MAIL_FROM_ADDRESS') . '>',
            'to' => [$email],
            'subject' => 'Mã OTP xác thực tài khoản MyShoes',
            'html' => "
                <h2>Mã OTP của bạn</h2>
                <p style='font-size:20px'><b>$otp</b></p>
                <p>Mã có hiệu lực trong <b>5 phút</b>.</p>
            ",
        ]);

        if ($response->failed()) {
            throw new \Exception('Gửi email OTP thất bại');
        }
    }
}
