<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('order_items', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('order_id'); // 所属订单id
      $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade'); // 外键
      $table->unsignedBigInteger('product_id'); // 对应商品id
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->unsignedBigInteger('product_sku_id');   // 对应商品skuid
      $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
      $table->unsignedInteger('amount');    // 数量
      $table->decimal('price', 10, 2);      // 单价
      $table->unsignedInteger('rating')->nullable();  // 用户打分
      $table->text('review')->nullable();       // 用户评价
      $table->timestamp('reviewed_at')->nullable();   // 评价时间
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('order_items');
  }
}
