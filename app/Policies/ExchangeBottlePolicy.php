<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ExchangeBottle;

class ExchangeBottlePolicy extends Policy
{
    public function update(User $user, ExchangeBottle $exchange_bottle)
    {
        // return $exchange_bottle->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, ExchangeBottle $exchange_bottle)
    {
        return true;
    }
}
