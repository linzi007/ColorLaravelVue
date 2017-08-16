<?php

namespace App\Observers;

use App\Models\MainOrderPayment;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class MainOrderPaymentObserver
{
    public function creating(MainOrderPayment $main_order_payment)
    {
        //
    }

    public function updating(MainOrderPayment $main_order_payment)
    {
        //
    }
}