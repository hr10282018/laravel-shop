<?php

namespace App\Listeners;

use DB;
use App\Models\OrderItem;
use App\Events\OrderReviewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// implements ShouldQueue 代表这个事件处理器是异步的
class UpdateProductRating implements ShouldQueue
{
  public function handle(OrderReviewed $event)
  {
    // 通过 with 方法提前加载数据，避免 N + 1 性能问题
    $items = $event->getOrder()->items()->with(['product'])->get();
    foreach ($items as $item) {
      $result = OrderItem::query()
        ->where('product_id', $item->product_id)
        ->whereNotNull('reviewed_at')
        ->whereHas('order', function ($query) {
          $query->whereNotNull('paid_at');
        })
        ->first([ // first()-接受一个数组作为参数，代表此次SQL要查询出来的字段
          // DB::raw()-会把DB::raw()里的参数原样拼接到SQL里,
          DB::raw('count(*) as review_count'),  // 类似-select `count(*) as review_count`
          DB::raw('avg(rating) as rating')
        ]);
      // 更新商品的评分和评价数
      $item->product->update([
        'rating'       => $result->rating,
        'review_count' => $result->review_count,
      ]);
    }
  }
}