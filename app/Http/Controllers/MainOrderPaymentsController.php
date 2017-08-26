<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ExcelTrait;
use App\Models\ExchangeBottle;
use App\Models\GoodsSetting;
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
use Mockery\Exception;

class MainOrderPaymentsController extends Controller
{
    use ExcelTrait;

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
    /**
     * @var GoodsSetting
     */
    private $goodsSetting;

    public function __construct(
        MainOrder $mainOrder
        , MainOrderPayment $mainOrderPayment
        , OrderGoodsPayment $orderGoodsPayment
        , OrderGoods $orderGoods
        , Order $order
        , SubOrderPayment $subOrderPayment
        , GoodsSetting $goodsSetting
    )
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->mainOrder = $mainOrder;
        $this->mainOrderPayment = $mainOrderPayment;
        $this->orderGoodsPayment = $orderGoodsPayment;
        $this->orderGoods = $orderGoods;
        $this->order = $order;
        $this->subOrderPayment = $subOrderPayment;
        $this->goodsSetting = $goodsSetting;
    }

    public function index(Request $request)
    {
        $where = $this->getWhere($request);
        $mainOrders = $this->mainOrder->list($where)->paginate($request->per_page, self::FIELDS, 'current_page');

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
            $orderGoodsPayments = $this->calculateOrderGoods($data, $data['id']);
            $mainOrderPayments = $this->calculateMainOrder($data, $orderGoodsPayments);
            $this->calculateSubOrder($mainOrderPayments);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            response('执行更新失败！', 400);
        }

        response('操作成功！');
    }

    public function getWhere(Request $request)
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
        if (isset($request->status) && in_array($request->status, [0, 1])) {
            $where['status'] = $request->status;
        }

        return $where;
    }

    /**
     * 保存 orderGoodsPayments 数据
     * @param array $orderGoodsPayments
     * @param bool $isUpdate 识别插入还是更新
     * @return mixed
     */
    private function calculateOrderGoods($orderGoodsPayments, $isUpdate = true)
    {
        foreach ($orderGoodsPayments as $goods) {
            $goodsPayment = $goods['payments'];
            //校验合计金额
            $shifaNumber = $goods['goods_num'] - $goodsPayment['quehuo_number'] - $goodsPayment['jushou_number'];
            $shifaAmount = round($shifaNumber * $goods['goods_price'], 2);

            //计算缺货金额
            $goodsPayment['id'] = $goods['rec_id'];
            $goodsPayment['shifa_number'] = $shifaNumber;
            $goodsPayment['shifa_amount'] = $shifaAmount;
            $goodsPayment = $this->goodsSetting->calculate($goodsPayment);

            $this->orderGoodsPayment->updateOrInsert(['id' => $goodsPayment['id']], $goodsPayment);

            //增加部分后面需要用的数据
            $goodsPayment['quehuo_amount'] = round($goodsPayment['quehuo_number'] * $goods['goods_price'], 2);
            $goodsPayment['jushou_amount'] = round($goodsPayment['jushou_number'] * $goods['goods_price'], 2);
            $goodsPayment['store_id'] = $goods['store_id'];

            $orderGoodsPayments[$goods['rec_id']] = $goodsPayment;
        }

        return $orderGoodsPayments;
    }

    /**
     * 保存主订单信息
     * @param array $mainOrder
     * @param $orderGoodsPayments
     * @return array
     */
    private function calculateMainOrder($mainOrder, $orderGoodsPayments)
    {
        $payments = [
            'pay_id'           => $mainOrder['pay_id'],
            'quehuo'           => $mainOrder['quehuo'],
            'jushou'           => $mainOrder['jushou'],
            'shifa'            => $mainOrder['shifa'],
            'qiandan'          => $mainOrder['qiandan'],
            'ziti'             => $mainOrder['ziti'],
            'qita'             => $mainOrder['qita'],
            'weicha'           => $mainOrder['weicha'],
            'desc_remark'      => $mainOrder['desc_remark'],
            'yingshou'         => $mainOrder['yingshou'],
            'pos'              => $mainOrder['pos'],
            'weixin'           => $mainOrder['weixin'],
            'alipay'           => $mainOrder['alipay'],
            'yizhifu'          => $mainOrder['yizhifu'],
            'out_pay_sn'       => $mainOrder['out_pay_sn'],
            'cash'             => $mainOrder['cash'],
            'shishou'          => $mainOrder['shishou'],
            'delivery_fee'     => $mainOrder['delivery_fee'],
            'driver_fee'       => $mainOrder['driver_fee'],
            'driver_id'        => $mainOrder['driver_id'],
            'second_driver_id' => $mainOrder['second_driver_id'],
            'jk_driver_id'     => $mainOrder['jk_driver_id'],
            'status'           => $mainOrder['status'],
            'jlr'              => $mainOrder['jlr'],
            'jzr'              => $mainOrder['jzr'],
            'updater'          => $mainOrder['updater'],
        ];

        $quehuo = $jushou = $shifa = 0;
        foreach ($orderGoodsPayments as $goods) {
            $quehuo += $goods['quehuo_amount'];
            $jushou += $goods['jushou_amount'];
            $shifa += $goods['shifa_amount'];
        }
        //缺货,拒收,实发
        $payments['jushou'] = $jushou;
        $payments['shifa'] = $shifa;
        $payments['quehuo'] = $shifa - $jushou;
        //$payments['quehuo'] = $quehuo;
        //应发,实收
        $payments['yingfa'] = $this->getYingfaAmount($payments);
        $payments['shishou'] = $this->getShishou($mainOrder);

        $result = $this->mainOrder->updateOrInsert(['pay_id' => $payments['pay_id']], $payments);
        if ($result === false) {
            throw new Exception('更新主收款登记簿失败');
        }

        return $payments;
    }

    /**
     * 应收=实发金额-签单-自提-其他-尾差
     *
     * @param $mainOrder
     * @return mixed
     */
    private function getYingfaAmount($mainOrder)
    {
        return $mainOrder['shifa'] - $mainOrder['qiandan']
            - $mainOrder['ziti'] - $mainOrder['qita'] - $mainOrder['weicha'];
    }

    /**
     * 实收=预存款+POS+微信+支付宝+现金
     *
     * @param $mainOrder
     * @return mixed
     */
    private function getShishou($mainOrder)
    {
        return $mainOrder['pd_amount'] + $mainOrder['pos'] + $mainOrder['weixin']
            + $mainOrder['alipay'] + $mainOrder['yizhifu'] + $mainOrder['cash'];
    }

    // @TODO 计算子单的金额及保存
    private function calculateSubOrder($mainOrderPayments)
    {
        $orderGoods = $this->orderGoods->with('payments')
            ->where('pay_id', $mainOrderPayments['pay_id'])->get();
        $storeGoods = [];
        foreach ($orderGoods as $goods) {
            $storeId = $goods['store_id'];
            $storeGoods[$storeId] = $goods;
        }

        //取得更新的数据
        $orderPayments = [];
        //子单相同的值
        $orderPayment = [
            'pay_id'       => $mainOrderPayments['pay_id'],
            'desc_remark'  => $mainOrderPayments['desc_remark'],
            'out_pay_sn'   => $mainOrderPayments['out_pay_sn'],
        ];
        //缺货,拒收,实发,百分比 需要根据实际发货计算
        foreach ($storeGoods as $storeId => $goodsList) {
            $orderPayment['quehuo'] = collect($goodsList)->sum('payments.quehuo_amount');
            $orderPayment['jushou'] = collect($goodsList)->sum('payments.jushou_amount');
            $orderPayment['shifa'] = collect($goodsList)->sum('payments.shifa');
            $orderPayment['percent'] = round($orderPayment[$storeId]['shifa']/$mainOrderPayments['shifa'], 2) * 100;

            $orderPayments[$storeId] = $orderPayment;
        }
        //修正百分比
        $orderPayments = fixArrayTotal($orderPayments, ['percent'], 100);

        //子单字段对应主单的内容
        $subOrderDefault = [
            'qiandan'      => $mainOrderPayments['qiandan'],
            'ziti'         => $mainOrderPayments['ziti'],
            'qita'         => $mainOrderPayments['qita'],
            'weicha'       => $mainOrderPayments['weicha'],
            'desc_remark'  => $mainOrderPayments['desc_remark'],
            'yingshou'     => $mainOrderPayments['yingshou'],
            'pos'          => $mainOrderPayments['pos'],
            'weixin'       => $mainOrderPayments['weixin'],
            'alipay'       => $mainOrderPayments['alipay'],
            'yizhifu'      => $mainOrderPayments['yizhifu'],
            'out_pay_sn'   => $mainOrderPayments['out_pay_sn'],
            'cash'         => $mainOrderPayments['cash'],
            'shishou'      => $mainOrderPayments['shishou'],
            'delivery_fee' => $mainOrderPayments['delivery_fee'],
            'driver_fee'   => $mainOrderPayments['driver_fee'],
        ];
        $insertOrUpdateData = [];
        //金额根据百分比分摊
        foreach ($orderPayments as $storeId => $subPayments) {
            $percentage = $subPayments['percent'] / 100;
            foreach ($subOrderDefault as $key => $value) {
                $subPayments[$key] = $value * $percentage;
            }
            $condition = [
                'pay_id'   => $subPayments['pay_id'],
                'store_id' => $subPayments['store_id'],
            ];
            $result = $this->orderGoodsPayment->updateOrInsert($condition, $insertOrUpdateData);
            if ($result) {
                throw new Exception('子单收款登记表更新失败!');
            }
        }
    }

    //重算配送费
    public function reCalculateShippingFee(Request $request)
    {
        if (empty($payIds = $request->get('pay_ids'))) {
            return $this->fail('请选择');
        }
        $payIds = explode(',', $payIds);
        $data = [];
        foreach ($payIds as $payId) {
            if (! $mainOrderPayments = $this->mainOrderPayment->where('pay_id', $payId)->first()) {
                return $this->fail('请录入之后，再重新计算');
            }

            if (1 == $mainOrderPayments['status']) {
                return $this->fail('已记账，请先执行反记账');
            }

            $orderGoodsPayments = $this->orderGoods->with('payments')->where('pay_id', $payId)->get()->pluck('payments');
            //查询payments
            $data[$payId] = $this->goodsSetting->calculateMulti($orderGoodsPayments);
        }

        return $this->success('完成重算', $data);
    }

    //重算司机费用
    public function reCalculateDriverFee(Request $request)
    {
        return $this->reCalculateShippingFee($request);
    }

    public function jizhang(Request $request)
    {
        if (empty($payIds = $request->get('pay_ids'))) {
            return $this->fail('请选择');
        }
        $payIds = explode(',', $payIds);
        $count = $this->mainOrderPayment->whereIn('pay_id', $payIds)->count();
        if (!$count) {
            return $this->fail('请先录入再执行记账');
        }
        if ($this->mainOrderPayment->jizhang($payIds)) {
            $this->success('记账成功');
        }

        return $this->fail('记账失败');
    }

    // @TODO 反记账
    public function fanjizhang(Request $request)
    {
        $payIds = $request->get('pay_ids');
        if (empty($payIds)) {
            return $this->fail('请选择');
        }
        $payIds = explode(',', $payIds);
        if ($this->mainOrderPayment->fanjizhang($payIds)) {
            return $this->success('反记账成功');
        }

        return $this->fail('反记账失败');
    }

    public function export(Request $request)
    {
        $this->mainOrder->list($this->getWhere($request))->export();
    }
}