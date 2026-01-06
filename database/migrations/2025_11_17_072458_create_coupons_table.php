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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id('coupon_id');
            $table->string('coupon_code', 50)->unique();

            $table->string('url_image');
            $table->string('public_url_image');

            $table->string('title')->nullable(); // 👈 tiêu đề hiển thị
            $table->text('description')->nullable();

            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->decimal('discount_value', 10, 2);

            $table->decimal('max_discount_amount', 10, 2)->nullable(); // 👈 NEW
            $table->decimal('min_purchase_amount', 10, 2)->nullable();

            $table->integer('usage_limit')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('coupon_code');
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
        Schema::dropIfExists('coupons');
    }
};
