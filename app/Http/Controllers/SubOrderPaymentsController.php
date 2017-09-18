<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SubOrderPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubOrderPaymentRequest;

class SubOrderPaymentsController extends Controller
{
    /**
     * @var SubOrderPayment
     */
    private $subOrderPayment;
    /**
     * @var Order
     */
    private $order;

    const FIELDS = [
        'order.order_sn', 'order.order_id'
        , 'order.pay_id', 'order.pay_sn', 'order.store_id', 'order.add_time', 'order.goods_amount'
        , 'order.promotion_amount', 'order.pd_amount', 'order.order_amount'
        , 'order.share_union_promotion as union_promotion', 'order.share_site_promotion as site_promotion'
        , 'main_order_payments.jk_at', 'main_order_payments.out_pay_sn', 'main_order_payments.jk_at'
        , 'main_order_payments.jk_driver_id', 'main_order_payments.remark', 'main_order_payments.status'
        , 'main_order_payments.jzr', 'main_order_payments.second_driver_id'
    ];

    public function __construct(Order $order, SubOrderPayment $subOrderPayment)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->subOrderPayment = $subOrderPayment;
        $this->order = $order;
    }

	public function index(Request $request)
	{
        $where = $this->getWhere();
        if ($request->filled('add_time') && 'null' != $request->add_time[0]) {
            $this->order = $this->order->whereBetween('order.add_time', $this->getRequestAddTime());
        }
        if ($paySn = $request->filled('pay_sn')) {
            $where['order.pay_sn'] = $paySn;
        }
        if ($request->filled('store_id')) {
            $where['order.store_id'] = $request->store_id;
        }
        if ($orderSn = $request->order_sn) {
            $where['order.order_sn'] = $orderSn;
        }
        if (isset($request->status) && in_array($request->status, [0, 1])) {
            $where['main_order_payments.status'] = $request->status;
        }
        if (isset($request->status) && in_array($request->status, [0, 1])) {
            $where['main_order_payments.status'] = $request->status;
        }
        if ($request->filled('jk_driver_id')) {
            $where['main_order_payments.jk_driver_id'] = $request->jk_driver_id;
        }
        if ($request->filled('jzr')) {
            $where['main_order_payments.jzr'] = $request->jzr;
        }
        $subOrderPayments = $this->order
            ->leftJoin('main_order_payments', 'order.pay_id', '=', 'main_order_payments.pay_id')
            ->with(['subOrderPayment'])
            ->where($where)->orderBy('order.pay_id', 'desc')->paginate(request()->per_page, self::FIELDS);
        $subOrderPayments->load(['store' => function($query) {
            $query->select('store_id', 'store_name');
        }]);
        $subOrderPayments = $subOrderPayments->toArray();
        $jkDriverIds = $jzrIds = $drivers = $admins = [];
        foreach ($subOrderPayments['data'] as $key => $item) {
            $jkDriverIds[] = $item['jk_driver_id'];
            $jzrIds[] = $item['jzr'];
            if (!empty($item['jk_driver'])) {
                $item['jk_driver_name'] = $item['jk_driver']['name'];
                unset($item['jk_driver']);
            }
            if (!empty($item['jz_admin'])) {
                $item['jzr_name'] = $item['jz_admin']['admin_name'];
                unset($item['jk_driver']);
            }
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
        foreach ($subOrderPayments['data'] as $key => $item) {
            if (!empty($item['jk_driver_id'])) {
                $item['jk_driver_name'] = empty($drivers[$item['jk_driver_id']]) ? $item['jk_driver_id'] : $drivers[$item['jk_driver_id']];
            }

            if (!empty($item['jzr'])) {
                $item['jzr_name'] = empty($admins[$item['jzr']]) ? $item['jzr'] : $admins[$item['jzr']];
            }
            $item['store']['store_name'] = empty($item['store']) ? $item['store_id'] : $item['store']['store_name'];
            if (!empty($item['sub_order_payment'])) {
                $item = array_merge($item['sub_order_payment'], $item);
                unset($item['sub_order_payment']);
            }
            $subOrderPayments['data'][$key] = $item;
        }
        return response($subOrderPayments);
	}

    private function getWhere()
    {
        $request = request();
        $where = [];
        $this->order->whereIn('main_order_payments.order_state', [30, 40]);
        if ($paySn = $request->pay_sn) {
            $data = $this->order->where('pay_sn', $paySn)->first(['pay_id']);
            if (empty($data)) {
                return $where = ['order.pay_id' => 0];
            }
            $where['order.pay_id'] = $data['pay_id'];
        }
        if ($request->add_time) {
            $timeStart = strtotime($request->add_time[0]);
            $timeEnd = strtotime($request->add_time[1]);
            $this->order->whereBetween('add_time', [$timeStart, $timeEnd]);
        }
        if ($request->jk_driver_id) {
            $where['main_order_payments.jk_driver_id'] = $request->jk_driver_id;
        }
        if ($request->jzr_id) {
            $where['main_order_payments.jzr_id'] = $request->jzr;
        }
        if (isset($request->status) && in_array($request->status, [0, 1])) {
            $where['main_order_payments.status'] = $request->status;
        }

        return $where;
    }

    public function export(Request $request)
    {
        return app('App\Services\SubOrderPaymentsExport')->excel($request->toArray());
    }
}