<?php

namespace App\Http\Controllers;

use App\Models\ExchangeBottle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExchangeBottleRequest;

class ExchangeBottlesController extends Controller
{
    /**
     * @var ExchangeBottle
     */
    private $exchangeBottle;

    public function __construct(ExchangeBottle $exchangeBottle)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->exchangeBottle = $exchangeBottle;
    }

	public function index(Request $request)
	{
        $where = [];
        if ($request->has('pay_sn')) {
            $where['pay_sn'] = $request->pay_sn;
        }
        if ($request->has('store_id')) {
            $where['store_id'] = $request->store_id;
        }

        if ($request->has('created_at') && 'null' != $request->created_at[0]) {
            $startAt = Carbon::parse($request->created_at[0])->toDateTimeString();
            $endAt = Carbon::parse($request->created_at[1])->toDateTimeString();
            $this->exchangeBottle = $this->exchangeBottle->whereBetween('created_at', [$startAt, $endAt]);
        }
        $exchange_bottles = $this->exchangeBottle->with(['store' => function ($query) {
            $query->select('store_id', 'store_name');
        }, 'driver' => function($query) {
            $query->select('id','name');
        }, 'admin' => function($query) {
            $query->select('admin_id', 'admin_name');
        }])->where($where)->paginate($request->per_page);

		return response($exchange_bottles);
	}

    public function show(ExchangeBottle $exchange_bottle)
    {
        response(compact('exchange_bottle'));
    }

	public function store(ExchangeBottleRequest $request)
	{
        if ($request->has('exchangeBottle')){
            $attributes = [
                'store_id'  => $request->store_id,
                'amount'    => $request->amount,
                'driver_id' => $request->driver_id,
                'pay_sn'    => $request->pay_sn,
                'creator'   => currentUserId(),
            ];

            $result = $this->exchangeBottle->create($attributes);
            if ($result) {
                return $this->success('保存成功');
            }
        }
        return $this->fail('保存失败');
	}
}