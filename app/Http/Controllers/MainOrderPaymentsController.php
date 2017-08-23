<?php

namespace App\Http\Controllers;

use App\Models\MainOrder;
use App\Models\MainOrderPayment;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderGoodsPayment;
use App\Models\SubOrderPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MainOrderPaymentRequest;
use Illuminate\Support\Facades\DB;

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
    /**
     * @var OrderGoodsPayment
     */
    private $orderGoodsPayment;
    /**
     * @var OrderGoods
     */
    private $orderGoods;
    /**
     * @var Order
     */
    private $order;
    /**
     * @var SubOrderPayment
     */
    private $subOrderPayment;

    const FIELDS = [
        'main_order_payments.*'
        , 'main_order.pay_id', 'main_order.pay_sn', 'main_order.add_time', 'main_order.pay_amount'
        , 'main_order.promotion_amount', 'main_order.pd_amount',
    ];
    public function __construct(
        MainOrder $mainOrder
        , MainOrderPayment $mainOrderPayment
        , OrderGoodsPayment $orderGoodsPayment
        , OrderGoods $orderGoods
        , Order $order
        , SubOrderPayment $subOrderPayment
    )
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->mainOrder = $mainOrder;
        $this->mainOrderPayment = $mainOrderPayment;
        $this->orderGoodsPayment = $orderGoodsPayment;
        $this->orderGoods = $orderGoods;
        $this->order = $order;
        $this->subOrderPayment = $subOrderPayment;
    }

    public function index(Request $request)
    {
        $where = [];
        $this->mainOrder->whereIn('main_order.order_state', [30, 40]);
        if ($paySn = $request->pay_sn) {
            $data = $this->mainOrder->where('pay_sn', $paySn)->first(['pay_id']);
            $where['main_order.pay_id'] = $data['pay_id'];
        }
        if ($request->add_time_start) {
            $where['main_order.add_time'] = ['>=', strtotime($request->add_time_start)];
        }
        if ($request->add_time_end) {
            $where['main_order.add_time'] = ['<=', strtotime($request->add_time_end)];
        }
        if ($request->pay_driver_id) {
            $where['main_order_payments.pay_driver_id'] = $request->pay_driver_id;
        }
        if ($request->jzr_id) {
            $where['main_order_payments.jzr_id'] = $request->jzr;
        }
        if (isset($request->status) && in_array($request->status, [0, 1, 2])) {
            $where['status'] = $request->status;
        }
        $mainOrders = $this->mainOrder->leftJoin('main_order_payments', 'main_order.pay_id', '=', 'main_order_payments.pay_id')
        ->where($where)->orderBy('main_order.pay_id', 'desc')->paginate(20, self::FIELDS);

        return response()->json($mainOrders);
    }

    //主订单详情
    public function show($pay_id)
    {
        $mainOrder = $this->mainOrder->leftJoin('main_order_payments', 'main_order.pay_id', '=', 'main_order_payments.pay_id')
            ->where(['main_order.pay_id' => $pay_id])->first(['main_order_payments.*', 'main_order.*']);
        $orderGoods = $this->orderGoods->with('payments')
            ->where('pay_id', $pay_id)->get();
        $mainOrder['goods_list'] = $orderGoods;
        return response()->json($mainOrder);
    }

    //更新
    public function store(Request $request)
    {
        $data = $request->all();
        DB::beginTransaction();
        try {
            $this->calculateOrderGoods($data);
            $this->calculateMainOrder($data);
            $this->calculateSubOrder($data);
            $this->recordExchangeBottle($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            response('执行更新失败！', 400);
        }

        response('操作成功！');
    }

    // @TODO 更换瓶盖
    private function recordExchangeBottle($data)
    {
        if (!$data['is_exchange_bottle'] || empty($data['exchange_bottle_store_id'])) {
            return true;
        }

        //换瓶盖新增
        return true;
    }

    // @TODO 计算单品账单
    private function calculateOrderGoods($data)
    {
        return true;
    }

    // @TODO 计算整单的金额及保存
    private function calculateMainOrder($data)
    {

    }

    // @TODO 计算子单的金额及保存
    private function calculateSubOrder($data)
    {
        //计算orderGoodsPayments的缺货金额
        return true;
    }
    // @TODO 记账
    public function jizhang(Request $request)
    {
        
    }

    // @TODO 反记账
    public function fanjizhang(Request $request)
    {

    }

    // @TODO 重算配送费
    public function reCalculateShippingFee(Request $request)
    {

    }

    // @TODO 导出
    public function export(Request $request)
    {
        
    }

    // @TODO 打印
    public function printPage(Request $request)
    {

    }
}