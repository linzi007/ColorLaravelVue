<?php

namespace App\Observers;

use App\Models\SubOrderPayment;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class SubOrderPaymentObserver
{
    public function creating(SubOrderPayment $sub_order_payment)
    {
        //
    }

    public function updating(SubOrderPayment $sub_order_payment)
    {
        //
    }
}