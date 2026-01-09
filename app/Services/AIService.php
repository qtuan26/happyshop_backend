<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('AI_SERVICE_URL');
    }

    /**
     * Gửi tin nhắn sang Chatbot AI
     * @param int|string $userId
     * @param string $message
     * @return string
     */
    public function chat($userId, $message)
    {
        try {
            $response = Http::timeout(120) 
    ->post("{$this->baseUrl}/api/chat", [
        'user_id' => (string) $userId,
        'message' => $message
    ]);

            if ($response->successful()) {
                return $response->json()['reply'];
            }
            
            Log::error('AI Chat Error: ' . $response->body());
            return 'Xin lỗi, hệ thống AI đang bận. Vui lòng thử lại sau.';
        } catch (\Exception $e) {
            Log::error('AI Connection Failed: ' . $e->getMessage());
            return 'Không thể kết nối đến trợ lý ảo.';
        }
    }

    /**
     * Lấy danh sách ID sản phẩm gợi ý
     * @param int|string $userId
     * @return array
     */
    public function getRecommendedProductIds($userId)
    {
        try {
            $response = Http::get("{$this->baseUrl}/api/recommend/{$userId}");
            
            if ($response->successful()) {
                $data = $response->json()['data'];
                // AI trả về list object [{id, score...}], ta chỉ cần lấy mảng ID
                return array_column($data, 'id');
            }
        } catch (\Exception $e) {
            Log::warning('AI Recommend Failed: ' . $e->getMessage());
        }
        return []; // Trả về rỗng nếu lỗi
    }
}