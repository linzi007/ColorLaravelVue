<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Driver;

class DriverPolicy extends Policy
{
    public function update(User $user, Driver $driver)
    {
        // return $driver->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, Driver $driver)
    {
        return true;
    }
}
