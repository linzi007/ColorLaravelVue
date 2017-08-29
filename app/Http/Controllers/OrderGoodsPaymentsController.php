<?php

namespace App\Http\Controllers;

use App\Models\MainOrderPayment;
use App\Models\OrderGoodsPayment;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderGoodsPaymentRequest;

class OrderGoodsPaymentsController extends Controller
{
    /**
     * @var OrderGoodsPayment
     */
    private $orderGoodsPayment;
    /**
     * @var MainOrderPayment
     */
    private $mainOrderPayment;

    const FIELDS = [
        'order_goods_payments.*'
        , 'main_order_payments.jk_at', 'main_order_payments.out_pay_sn', 'main_order_payments.jk_at'
        , 'main_order_payments.jk_driver_id', 'main_order_payments.remark', 'main_order_payments.status'
        , 'main_order_payments.jzr'
    ];

    public function __construct(OrderGoodsPayment $orderGoodsPayment, MainOrderPayment $mainOrderPayment)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->orderGoodsPayment = $orderGoodsPayment;
        $this->mainOrderPayment = $mainOrderPayment;
    }

	public function index()
	{
        $this->orderGoodsPayment->leftJoin('main_order_payments', 'order_goods_payments.pay_id', '=', 'main_order_payments.pay_id')
            ->where($where[])->orderBy('sub_order_payments.pay_id', 'desc')->paginate(request()->per_page, self::FIELDS);

	}

    public function getWhere()
    {
        $request = request()->all();
        if (request()->has('add_time')) {
            $timeStart = strtotime(request()->add_time[0]);
            $timeEnd = strtotime(request()->add_time[1]);
            $this->orderGoodsPayment->whereBetween('main_order_payments.add_time', [$timeStart, $timeEnd]);
        }

        if (request()->has('store_id')) {

        }

        if (request()->has('pay_sn')) {

        }

        if (request()->has('order_sn')) {

        }

        if (in_array(request()->get('status'), [0, 1])) {

        }

        if (request()->has('jk_driver_id')) {

        }

        if (request()->has('jzr')) {

        }

        if (request()->has('has_second_driver')) {

        }
    }
}