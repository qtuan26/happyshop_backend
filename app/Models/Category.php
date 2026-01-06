<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories'; // optional (Laravel tự hiểu)
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'description',
    ];

    // 1 Category có nhiều Product
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}
