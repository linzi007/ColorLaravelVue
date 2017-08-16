<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainOrder extends Model
{
    protected $table = 'main_order';

    protected $primaryKey = 'main_order_id';

    /**
     * suborders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function SubOrders()
    {
        return $this->hasMany(\App\Models\Order::class, 'pay_id', 'pay_id');
    }

    /**
     * order goods
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function OrderGoods()
    {
        return $this->hasMany(\App\Models\OrderGoods::class, 'pay_id', 'pay_id');
    }

    /**
     * order goods
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function MainOrderPayment()
    {
        return $this->hasOne(\App\Models\OrderGoods::class, 'pay_id', 'pay_id');
    }
}
