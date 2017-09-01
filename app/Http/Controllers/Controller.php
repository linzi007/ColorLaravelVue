<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 构造函数
    **/
    public function __construct(){
        if (request()->has('sql')) {
            getSql();
        }
    }

    public function success($msg = '操作成功', $data = [])
    {
        return response(['status' => 1, 'msg' => $msg, 'data' => $data]);
    }

    public function fail($msg, $data = [])
    {
        return response(['status' => 0, 'msg' => $msg, 'data' => $data]);
    }

    public function getRequestAddTime()
    {
        $timeStart = strtotime(request()->add_time[0]);
        $timeEnd = strtotime(request()->add_time[1]);

        return [$timeStart, $timeEnd];
    }
}
