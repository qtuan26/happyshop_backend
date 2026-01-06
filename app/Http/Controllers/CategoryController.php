<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;


class CategoryController extends Controller
{
    // GET /api/categories
    public function index()
    {
        return response()->json([
            'data' => Category::select('category_id', 'category_name')->get()
        ]);
    }

    // GET /api/categories/{id}/products
    public function products($id)
    {
        $products = Category::with(['products' => function ($query) {
            $query->select(
                'product_id',
                'category_id',
                'product_name',
                'base_price',
                'url_image'
            )->where('is_active', true);
        }])->findOrFail($id);

        return response()->json([
            'category' => [
                'category_id' => $products->category_id,
                'category_name' => $products->category_name,
            ],
            'products' => $products->products
        ]);
    }
    public function filterProducts(Request $request, $categoryId)
    {
        $query = Product::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true);

        /* =====================
        1. Lọc theo giá tối thiểu
        ===================== */
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        /* =====================
        2. Lọc theo giới tính
        Male / Female / Unisex
        ===================== */
        if ($request->filled('gender')) {
            $genders = $request->gender; // ['Male', 'Female']

            $query->where(function ($q) use ($genders) {
                $q->whereIn('gender', $genders)
                ->orWhere('gender', 'Unisex');
            });
        }

        // 3. Size (qua inventory)
        if ($request->filled('sizes')) {
            $sizes = $request->sizes;

            $query->whereHas('inventory', function ($q) use ($sizes) {
                $q->whereIn('size', $sizes)
                ->where('quantity', '>', 0);
            });
        }

        $products = $query->select(
            'product_id',
            'product_name',
            'base_price',
            'url_image',
            'gender'
        )->get();

        return response()->json([
            'data' => $products
        ]);
    }

    


}
