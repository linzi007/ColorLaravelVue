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
        , 'main_order_payments.jk_at', 'main_order_payments.out_pay_sn', 'main_order_payments.add_time', 'main_order_payments.pay_sn', 'main_order_payments.jk_at'
        , 'main_order_payments.jk_driver_id', 'main_order_payments.remark', 'main_order_payments.status'
        , 'main_order_payments.jzr'
    ];

    public function __construct(OrderGoodsPayment $orderGoodsPayment, MainOrderPayment $mainOrderPayment)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->orderGoodsPayment = $orderGoodsPayment;
        $this->mainOrderPayment = $mainOrderPayment;
    }

	public function index(Request $request)
	{
        $where = [];
        if ($request->has('add_time')&& 'null' != $request->add_time[0]) {
            $where[] = ['main_order_payments.add_time', 'between', $this->getRequestAddTime()];
            //$this->orderGoodsPayment->whereBetween('main_order_payments.add_time', $this->getRequestAddTime());
        }

        if ($request->has('store_id')) {
            $where['order_goods_payments.store_id'] = $request->store_id;
        }

        if ($request->has('pay_sn')) {
            $where['main_order_payments.pay_sn'] = $request->pay_sn;
        }

        if ($request->has('status') && in_array($request->status, [0, 1])) {
            $where['main_order_payments.status'] = $request->status;
        }

        if ($request->has('jk_driver_id')) {
            $where['main_order_payments.jk_driver_id'] = $request->jk_driver_id;
        }

        if ($request->has('jzr')) {
            $where['main_order_payments.jzr'] = $request->jzr;
        }

        if ($request->has('is_second_delivery')) {
            if (1 == $request->is_second_delivery) {
                $where['main_order_payments.jk_driver_id'] = ['gt', 0];
            } else {
                $where[] = ['main_order_payments.jk_driver_id', '=', null];
            }
        }

        $orderGoodsPayments = $this->orderGoodsPayment->leftJoin('main_order_payments', 'order_goods_payments.pay_id', '=', 'main_order_payments.pay_id')
            ->with(['orderGoods'])->where($where)->orderBy('main_order_payments.pay_id', 'desc')->paginate(request()->per_page, self::FIELDS);
        $orderGoodsPayments = $orderGoodsPayments->toArray();
        foreach ($orderGoodsPayments['data'] as $key => $payment) {
            if ($payment['order_goods']) {
                $payment = array_merge($payment, $payment['order_goods']);
                unset($payment['order_goods']);
                $orderGoodsPayments['data'][$key] = $payment;
            }
        }

        return response()->json($orderGoodsPayments);
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