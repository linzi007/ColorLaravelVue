<?php

namespace App\Observers;

use App\Models\OrderGoodsPayment;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class OrderGoodsPaymentObserver
{
    public function creating(OrderGoodsPayment $order_goods_payment)
    {
        //
    }

    public function updating(OrderGoodsPayment $order_goods_payment)
    {
        //
    }
}