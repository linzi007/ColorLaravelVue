<?php

namespace App\Http\Controllers;

use App\Models\MainOrderPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MainOrderPaymentRequest;

class MainOrderPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index()
	{
		$main_order_payments = MainOrderPayment::paginate();
		return view('main_order_payments.index', compact('main_order_payments'));
		return response($main_order_payments);
	}

    public function show(MainOrderPayment $main_order_payment)
    {
      response(compact('main_order_payment'))
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
}