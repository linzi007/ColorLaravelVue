<?php

namespace App\Http\Controllers;

use App\Models\SubOrderPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubOrderPaymentRequest;

class SubOrderPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index()
	{
		$sub_order_payments = SubOrderPayment::paginate();
		return view('sub_order_payments.index', compact('sub_order_payments'));
		return response($sub_order_payments);
	}

    public function show(SubOrderPayment $sub_order_payment)
    {
      response(compact('sub_order_payment'))
    }

	public function store(SubOrderPaymentRequest $request)
	{
		$sub_order_payment = SubOrderPayment::create($request->all());
	  return response(['id'=>$sub_order_payment->id, 'message'=>'Created successfully.']);
	}

	public function update(SubOrderPaymentRequest $request, SubOrderPayment $sub_order_payment)
	{
		$this->authorize('update', $sub_order_payment);
		$sub_order_payment->update($request->all());
	  return response(['id'=>$sub_order_payment->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(SubOrderPayment $sub_order_payment)
	{
		$this->authorize('destroy', $sub_order_payment);
		$sub_order_payment->delete();

    response(['message' => 'Deleted successfully.']);
  }
}