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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');

            $table->foreignId('customer_id')
                ->constrained('customers', 'customer_id');

            $table->timestamp('order_date')->useCurrent();

            // Tổng tiền sản phẩm
            $table->decimal('subtotal', 10, 2);

            // Phí ship cố định
            $table->decimal('shipping_fee', 10, 2)->default(20.00);

            // Tổng tiền giảm
            $table->decimal('discount_amount', 10, 2)->default(0.00);

            // Tổng thanh toán cuối cùng
            $table->decimal('total_amount', 10, 2);

            $table->string('payment_method')->nullable();
            $table->string('status')->default('pending');

            $table->timestamps();

            $table->index('customer_id');
            $table->index('status');
            $table->index('order_date');
        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
