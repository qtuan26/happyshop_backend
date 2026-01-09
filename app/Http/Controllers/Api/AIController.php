<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Models\Product;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    // Endpoint: POST /api/ai/chat
    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        
        // Lấy User ID từ Sanctum (User đang đăng nhập)
        $user = $request->user();
        
        $reply = $this->aiService->chat($user->id, $request->message);

        return response()->json([
            'message' => $reply,
            'sender' => 'ai'
        ]);
    }

    // Endpoint: GET /api/products/recommended
    public function recommendations(Request $request)
    {
        $user = $request->user();
        
        // Lấy danh sách ID từ AI
        $productIds = $this->aiService->getRecommendedProductIds($user->id);

        // Query lại Database Laravel để lấy đầy đủ thông tin sản phẩm (ảnh, giá...)
        if (!empty($productIds)) {
            // Sắp xếp đúng theo thứ tự AI gợi ý
            $idsString = implode(',', $productIds);
            $products = Product::whereIn('product_id', $productIds)
                ->orderByRaw("FIELD(product_id, $idsString)")
                ->get();
        } else {
            // Fallback: Nếu AI lỗi hoặc user mới, lấy sản phẩm bán chạy/mới nhất
            $products = Product::where('is_active', 1)->latest()->take(5)->get();
        }

        return response()->json(['data' => $products]);
    }
}