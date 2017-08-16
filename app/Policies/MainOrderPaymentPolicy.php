<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MainOrderPayment;

class MainOrderPaymentPolicy extends Policy
{
    public function update(User $user, MainOrderPayment $main_order_payment)
    {
        // return $main_order_payment->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, MainOrderPayment $main_order_payment)
    {
        return true;
    }
}
