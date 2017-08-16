<?php

namespace App\Http\Controllers;

use App\Models\OrderGoodsPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderGoodsPaymentRequest;

class OrderGoodsPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index()
	{
		$order_goods_payments = OrderGoodsPayment::paginate();
		return view('order_goods_payments.index', compact('order_goods_payments'));
		return response($order_goods_payments);
	}

    public function show(OrderGoodsPayment $order_goods_payment)
    {
      response(compact('order_goods_payment'))
    }

	public function store(OrderGoodsPaymentRequest $request)
	{
		$order_goods_payment = OrderGoodsPayment::create($request->all());
	  return response(['id'=>$order_goods_payment->id, 'message'=>'Created successfully.']);
	}

	public function update(OrderGoodsPaymentRequest $request, OrderGoodsPayment $order_goods_payment)
	{
		$this->authorize('update', $order_goods_payment);
		$order_goods_payment->update($request->all());
	  return response(['id'=>$order_goods_payment->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(OrderGoodsPayment $order_goods_payment)
	{
		$this->authorize('destroy', $order_goods_payment);
		$order_goods_payment->delete();

    response(['message' => 'Deleted successfully.']);
  }
}