<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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

    public function fail($msg, $data = [])
    {
        return response(['status' => 0, 'msg' => $msg, 'data' => $data]);
    }

    public function getRequestAddTime($timeStamp = true)
    {
        if ($timeStamp) {
            $timeStart = Carbon::parse(request()->add_time[0])->timestamp;
            $timeEnd = Carbon::parse(request()->add_time[1])->timestamp;
        } else {
            $timeStart = Carbon::parse(request()->add_time[0])->toDateTimeString();
            $timeEnd = Carbon::parse(request()->add_time[1])->toDateTimeString();
        }

        return [$timeStart, $timeEnd];
    }
}
