<?php

namespace App\Http\Controllers;

use App\Models\MainOrderPayment;
use App\Models\OrderGoodsPayment;
use Carbon\Carbon;
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
        , 'main_order_payments.jk_at', 'main_order_payments.out_pay_sn', 'main_order_payments.add_time'
        , 'main_order_payments.pay_sn', 'main_order_payments.jk_at'
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
        if ($request->filled('add_time')&& 'null' != $request->add_time[0]) {
            $this->orderGoodsPayment = $this->orderGoodsPayment->whereBetween('main_order_payments.add_time', $this->getRequestAddTime(true));
        }

        if ($request->filled('store_id')) {
            $where['order_goods_payments.store_id'] = $request->store_id;
        }

        if ($request->filled('pay_sn')) {
            $where['main_order_payments.pay_sn'] = $request->pay_sn;
        }

        if ($request->filled('status') && in_array($request->status, [0, 1])) {
            $where['main_order_payments.status'] = $request->status;
        }

        if ($request->filled('jk_driver_id')) {
            $where['main_order_payments.jk_driver_id'] = $request->jk_driver_id;
        }

        if ($request->filled('jzr')) {
            $where['main_order_payments.jzr'] = $request->jzr;
        }

        if ($request->filled('is_second_delivery')) {
            if (1 == $request->is_second_delivery) {
                $where['main_order_payments.jk_driver_id'] = ['gt', 0];
            } else {
                $where[] = ['main_order_payments.jk_driver_id', '=', null];
            }
        }
        $stores = app(\App\Models\Store::class)->getStoreCache();
        $stores = array_column($stores, 'store_name', 'store_id');
        $orderGoodsPayments = $this->orderGoodsPayment->leftJoin('main_order_payments', 'order_goods_payments.pay_id', '=', 'main_order_payments.pay_id')
            ->with(['orderGoods'])->where($where)->orderBy('main_order_payments.pay_id', 'desc')->paginate(request()->per_page, self::FIELDS);
        $orderGoodsPayments = $orderGoodsPayments->toArray();
        $jkDriverIds = $jzrIds = $drivers = $admins = [];
        foreach ($orderGoodsPayments['data'] as $key => $payment) {
            $jkDriverIds[] = $payment['jk_driver_id'];
            $jzrIds[] = $payment['jzr'];
            if (!empty($payment['jk_driver'])) {
                $payment['jk_driver_name'] = $payment['jk_driver']['name'];
                unset($payment['jk_driver']);
            }
            if (!empty($payment['jz_admin'])) {
                $payment['jzr_name'] = $payment['jz_admin']['admin_name'];
                unset($payment['jk_driver']);
            }
            //处理order_goods
            if ($payment['order_goods']) {
                $payment = array_merge($payment, $payment['order_goods']);
                unset($payment['order_goods']);
            }
            $payment['store_name'] = empty($stores[$payment['store_id']]) ? $payment['store_id'] : $stores[$payment['store_id']];
            $orderGoodsPayments['data'][$key] = $payment;
        }

        if ($jkDriverIds) {
            $drivers = app(\App\Models\Driver::class)->whereIn('id', array_unique($jkDriverIds))->get()->toArray();
            if (!empty($drivers)) {
                $drivers = array_column($drivers, 'name', 'id');
            }
        }
        if ($jzrIds) {
            $admins = app(\App\Models\Admin::class)->whereIn('admin_id', array_unique($jzrIds))->get()->toArray();
            if ($admins) {
                $admins = array_column($admins, 'admin_name', 'admin_id');
            }
        }

        foreach ($orderGoodsPayments['data'] as $key => $item) {
            if (!empty($item['jk_driver_id'])) {
                $item['jk_driver_name'] = empty($drivers[$item['jk_driver_id']]) ? $item['jk_driver_id'] : $drivers[$item['jk_driver_id']];
            }

            if (!empty($item['jzr'])) {
                $item['jzr_name'] = empty($admins[$item['jzr']]) ? $item['jzr'] : $admins[$item['jzr']];
            }
            $item['add_time'] = Carbon::createFromTimestamp($item['add_time'])->toDateTimeString();
            $orderGoodsPayments['data'][$key] = $item;
        }

        return response()->json($orderGoodsPayments);
	}

    public function export(Request $request)
    {
        return app('App\Services\OrderGoodsPaymentsExport')->excel($request->toArray());
    }
}