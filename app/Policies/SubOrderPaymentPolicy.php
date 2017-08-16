<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubOrderPayment;

class SubOrderPaymentPolicy extends Policy
{
    public function update(User $user, SubOrderPayment $sub_order_payment)
    {
        // return $sub_order_payment->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, SubOrderPayment $sub_order_payment)
    {
        return true;
    }
}
