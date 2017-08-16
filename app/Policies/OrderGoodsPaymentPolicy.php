<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrderGoodsPayment;

class OrderGoodsPaymentPolicy extends Policy
{
    public function update(User $user, OrderGoodsPayment $order_goods_payment)
    {
        // return $order_goods_payment->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, OrderGoodsPayment $order_goods_payment)
    {
        return true;
    }
}
