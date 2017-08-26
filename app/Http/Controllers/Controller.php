<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($msg = '操作成功', $data = [])
    {
        return response(['status' => 1, 'msg' => $msg, 'data' => $data]);
    }

    public function fail($msg)
    {
        return response(['status' => 0, 'msg' => $msg]);
    }
}
