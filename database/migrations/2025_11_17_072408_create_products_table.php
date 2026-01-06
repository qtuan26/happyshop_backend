<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('brand_id')->constrained('brands', 'brand_id');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->string('product_name');
            $table->string('url_image');
            $table->string('public_url_image');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('gender')->nullable();
            $table->date('date_added');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('brand_id');
            $table->index('category_id');
            $table->index('is_active');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
