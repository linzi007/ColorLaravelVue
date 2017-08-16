<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GoodsSetting;

class GoodsSettingPolicy extends Policy
{
    public function update(User $user, GoodsSetting $goods_setting)
    {
        // return $goods_setting->user_id == $user->id;
        return true;
    }

    public function destroy(User $user, GoodsSetting $goods_setting)
    {
        return true;
    }
}
