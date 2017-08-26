<?php

namespace App\Http\Controllers;

use App\Models\ExchangeBottle;
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
        $exchange_bottles = $this->exchangeBottle->paginate($request->per_page, ['*'], 'current_page');
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