<?php

namespace App\Http\Controllers;

use App\Models\MainOrder;
use App\Models\MainOrderPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MainOrderPaymentRequest;

class MainOrderPaymentsController extends Controller
{
    /**
     * @var MainOrder
     */
    private $mainOrder;
    /**
     * @var MainOrderPayment
     */
    private $mainOrderPayment;

    public function __construct(MainOrder $mainOrder, MainOrderPayment $mainOrderPayment)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->mainOrder = $mainOrder;
        $this->mainOrderPayment = $mainOrderPayment;
    }

	public function index()
	{
		$main_order_payments = MainOrderPayment::paginate();
		return view('main_order_payments.index', compact('main_order_payments'));
		return response($main_order_payments);
	}

    public function show(MainOrderPayment $main_order_payment)
    {
        response(compact('main_order_payment'));
    }

	public function store(MainOrderPaymentRequest $request)
	{
		$main_order_payment = MainOrderPayment::create($request->all());
	  return response(['id'=>$main_order_payment->id, 'message'=>'Created successfully.']);
	}

	public function update(MainOrderPaymentRequest $request, MainOrderPayment $main_order_payment)
	{
		$this->authorize('update', $main_order_payment);
		$main_order_payment->update($request->all());
	  return response(['id'=>$main_order_payment->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(MainOrderPayment $main_order_payment)
	{
		$this->authorize('destroy', $main_order_payment);
		$main_order_payment->delete();

        response(['message' => 'Deleted successfully.']);
    }

    public function mainIndex(Request $request)
    {
        $params = $request->all();
        $where = [];
        if ($paySn = $params['pay_sn']) {
            $pay_id = $this->mainOrder->where(['pay_sn', $paySn])->first(['pay_id']);
            $where['main_order.pay_id'] = $pay_id;
        }
        if ($params['add_time_start']) {
            $where['main_order.add_time'] = ['>=', strtotime($params['add_time_start'])];
        }
        if ($params['add_time_end']) {
            $where['main_order.add_time'] = ['<=', strtotime($params['add_time_end'])];
        }
        if ($params['pay_driver_id']) {
            $where['main_order_payments.pay_driver_id'] = $params['pay_driver_id'];
        }
        if ($params['jzr_id']) {
            $where['main_order_payments.jzr_id'] = $params['jzr_id'];
        }
        if (isset($params['status']) && in_array($params['status'], [0, 1, 2])) {
            $where['status'] = $params['status'];
        }

        $fields = 'main_order.pay_id,main_order.pay_sn,main_order.add_time,main_order.pay_amount'.
            'main_order.promotion_amount, main_order.pd_amount'.
            'main_order_payments.*';
        $mainOrders = $this->mainOrder->leftJoin('main_order_payments', 'pay_id', '=', 'pay_id')
        ->where($where)->paginate(20, $fields);

        response(compact('mainOrders'));
    }
}