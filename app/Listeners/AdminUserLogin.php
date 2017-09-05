<?php


namespace App\Listeners;


use Carbon\Carbon;
use Illuminate\Auth\Events\Login;

class AdminUserLogin
{
    public function __construct()
    {
        //
    }

    /**
     * 登录成功后更新信息
     * @param Login $event
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $user->admin_login_time = Carbon::now()->timestamp;
        $user->admin_login_num = $user->admin_login_num + 1;
        $user->save();

        //新增 admin_log

    }

}