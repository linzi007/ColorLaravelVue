<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ExcelTrait;
use App\Models\GoodsSetting;
use App\Models\MainOrder;
use App\Models\MainOrderPayment;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\OrderGoodsPayment;
use App\Models\SubOrderPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        , 'main_order.promotion_amount', 'main_order.pd_amount', 'main_order.goods_amount', 'main_order.order_amount'
        , 'main_order.union_promotion', 'main_order.site_promotion',
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
        $sortOrderMap = [
            'ascending' => 'asc', 'descending' => 'desc',
        ];
        $sortBy = 'pay_sn';
        $sortOrder = 'desc';
        if ($request->sort_by && in_array($request->sort_by, ['pay_sn'])) {
            $sortBy = $request->sort_by;
        }
        if ($request->sort_order && in_array($request->sort_order, ['ascending', 'descending'])) {
            $sortOrder = $sortOrderMap[$request->sort_order];
        }

        $condition = [];
        $mainOrders = $this->mainOrder->whereIn('order_state', [30, 40]);
        if ($request->jk_driver_id) {
            $condition['jk_driver_id'] = $request->jk_driver_id;
        }
        if ($request->jzr) {
            $condition['jzr'] = $request->jzr;
        }
        if (isset($request->status) && in_array($request->status, [0, 1])) {
            $condition['status'] = $request->status;
        } else if(2 == $request->status) {
            $mainOrders = $mainOrders->doesntHave('mainOrderPayment');
        }

        if ($condition) {
            $mainOrders = $mainOrders->whereHas('mainOrderPayment', function ($query) use ($condition) {
                $query->where($condition);
            });
        }

        $where = [];
        if ($paySn = $request->pay_sn) {
            $where['pay_sn'] = $paySn;
        }
        if ($request->add_time && 'null' != $request->add_time[0]) {
            $mainOrders = $mainOrders->whereBetween('main_order.add_time', $this->getRequestAddTime());
        }
        $mainOrders = $mainOrders->with(['mainOrderPayment', 'mainOrderPayment.jkDriver', 'mainOrderPayment.jzAdmin'])
            ->where($where)->orderBy($sortBy, $sortOrder)->paginate($request->per_page)->toArray();
        foreach ($mainOrders['data'] as $key => $item) {
            if (!empty($item['main_order_payment']['id'])) {
                $payment = $item['main_order_payment'];
                $payment['jk_driver_name'] = empty($payment['jk_driver']) ? $payment['jk_driver_id'] : $payment['jk_driver']['name'];
                $payment['jzr_name'] = empty($payment['jz_admin']) ? $payment['jzr'] : $payment['jz_admin']['admin_name'];
                unset($item['main_order_payment']);
                unset($payment['jz_admin']);
                unset($payment['jk_driver']);
                $mainOrders['data'][$key] = array_merge($item, $payment);
            }
        }
        return response()->json($mainOrders);
    }

    //主订单详情
    public function show($pay_id)
    {
        if (empty($pay_id)) {
            return $this->fail('参数错误');
        }
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
        if ($data['id']) {
            $result = $this->mainOrderPayment->where('status', 0)->find($data['id']);
            if (! $result) {
                return $this->fail('订单状态不正确');
            }
        }
        try {
            $orderGoodsPayments = $this->calculateOrderGoods($data, $data['id']);
            $mainOrderPayments = $this->calculateMainOrder($data, $orderGoodsPayments);
            $this->calculateSubOrder($mainOrderPayments);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->fail('保存失败：'.$e->getMessage());
        }
        return $this->success('保存成功');
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
            $where['main_order_payments.jk_driver_id'] = $request->jk_driver_id;
        }
        if ($request->jzr) {
            $where['main_order_payments.jzr'] = $request->jzr;
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
    private function calculateOrderGoods($mainOrder, $isUpdate = true)
    {
        $newGoodsPayments = [];
        $orderGoodsPayments = $mainOrder['goods_list'];
        //取得 store_id 对应的 order_sn
        $storeOrderMap = $this->getStoreOrderMap($mainOrder['pay_id']);
        foreach ($orderGoodsPayments as $goods) {
            $goodsPayment = $goods['payments'];
            //校验合计金额
            $goodsPayment['goods_id'] = $goods['goods_id'];
            $shifaNumber = $goods['goods_num'] - $goodsPayment['quehuo_number'] - $goodsPayment['jushou_number'];
            $shifaAmount = round($shifaNumber * $goods['goods_price'], 2);
            //计算缺货金额
            $goodsPayment['shifa_number'] = $shifaNumber;
            $goodsPayment['shifa_amount'] = $shifaAmount;
            $goodsPayment = $this->goodsSetting->calculate($goodsPayment);
            if (empty($goodsPayment)) {
                throw new Exception('货品配送费用未设置：' . $goods['goods_id']);
            }
            if (!empty($goodsPayment['id'])) {
                $result = $this->orderGoodsPayment->where('id', $goodsPayment['id'])->update($goodsPayment);
                if ($result === false) {
                    throw new Exception('更新明细失败!');
                }
            } else {
                //组织冗余字段,查询导出需要
                $goodsPayment['order_id'] = $goods['order_id'];
                $goodsPayment['store_id'] = $goods['store_id'];
                $goodsPayment['order_sn'] = $storeOrderMap[$goods['store_id']]['order_sn'];

                $goodsPayment['id'] = $goods['rec_id'];
                $insertData[] = $goodsPayment;
            }
            //增加部分后面需要用的数据
            $goodsPayment['quehuo_amount'] = round($goodsPayment['quehuo_number'] * $goods['goods_price'], 2);
            $goodsPayment['jushou_amount'] = round($goodsPayment['jushou_number'] * $goods['goods_price'], 2);
            $goodsPayment['store_id'] = $goods['store_id'];

            $newGoodsPayments[] = $goodsPayment;
        }

        if (!empty($insertData)) {
            $result = $this->orderGoodsPayment->insert($insertData);
            if ($result === false) {
                throw new Exception('新增明细失败!');
            }
        }
        return $newGoodsPayments;
    }

    private function getStoreOrderMap($payId)
    {
        return $this->order->select(['order_sn', 'store_id', 'order_id'])->where('pay_id', $payId)->get()->keyBy('store_id')->toArray();
    }

    /**
     * 保存主订单信息
     *
     * @param $mainOrder
     * @param $orderGoodsPayments
     * @return \Illuminate\Database\Eloquent\Model | MainOrderPayment
     */
    private function calculateMainOrder($mainOrder, $orderGoodsPayments)
    {
        $payments = [
            'pay_id'           => $mainOrder['pay_id'],
            'pay_sn'           => $mainOrder['pay_sn'],
            'add_time'         => $mainOrder['add_time'],
            'quehuo'           => $mainOrder['quehuo'],
            'jushou'           => $mainOrder['jushou'],
            'qiandan'          => $mainOrder['qiandan'],
            'ziti'             => $mainOrder['ziti'],
            'qita'             => $mainOrder['qita'],
            'weicha'           => $mainOrder['weicha'],
            'desc_remark'      => $mainOrder['desc_remark'],
            'pos'              => $mainOrder['pos'],
            'weixin'           => $mainOrder['weixin'],
            'alipay'           => $mainOrder['alipay'],
            'yizhifu'          => $mainOrder['yizhifu'],
            'out_pay_sn'       => $mainOrder['out_pay_sn'],
            'cash'             => $mainOrder['cash'],
            'delivery_fee'     => $mainOrder['delivery_fee'],
            'driver_fee'       => $mainOrder['driver_fee'],
            'second_driver_id' => $mainOrder['second_driver_id'],
            'jk_driver_id'     => $mainOrder['jk_driver_id'],
            'updater'          => currentUserId(),
            'jlr'              => currentUserId(),
            'remark'           => $mainOrder['remark'],
            'jk_at'           => date('Y-m-d H:i:s', strtotime($mainOrder['jk_at'])),
            'ck_at'           => date('Y-m-d H:i:s', strtotime($mainOrder['ck_at'])),
        ];
        $quehuo = $jushou = $shifa = $delivery_fee = $driver_fee = 0;
        foreach ($orderGoodsPayments as $goods) {
            $quehuo += $goods['quehuo_amount'];
            $jushou += $goods['jushou_amount'];
            $shifa += $goods['shifa_amount'];
            $delivery_fee += $goods['delivery_fee'];
            $driver_fee += $goods['driver_fee'];
        }
        //缺货,拒收,实发
        $payments['jushou'] = $jushou;
        $payments['shifa'] = $shifa;
        $payments['quehuo'] = $quehuo;

        $payments['delivery_fee'] = $delivery_fee;
        $payments['driver_fee'] = $driver_fee;
        //应收,实收
        $payments['yingshou'] = $this->getYingshouAmount($payments);
        $payments['shishou'] = $this->getShishou($mainOrder);
        $mainOrderPayment = $this->mainOrderPayment->updateOrCreate(['pay_id' => $payments['pay_id']], $payments);
        if (!$mainOrderPayment) {
            throw new Exception('更新主收款登记簿失败');
        }

        return $mainOrderPayment;
    }

    /**
     * 应收=实发金额-签单-自提-其他-尾差
     *
     * @param $mainOrder
     * @return mixed
     */
    private function getYingshouAmount($mainOrder)
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
    private function calculateSubOrder(MainOrderPayment $mainOrderPayments)
    {
        $orderGoods = $this->orderGoods->with('payments')
            ->where('pay_id', $mainOrderPayments['pay_id'])->get();
        $storeGoods = [];
        foreach ($orderGoods as $goods) {
            $storeId = $goods['store_id'];
            $storeGoods[$storeId][] = $goods;
        }
        //取得更新的数据
        $orderPayments = [];
        //子单相同的值
        $orderPaymentDefault = [
            'pay_id'       => $mainOrderPayments['pay_id'],
            'pay_sn'       => $mainOrderPayments['pay_sn'],
            'add_time'     => $mainOrderPayments['add_time'],
            'desc_remark'  => $mainOrderPayments['desc_remark'],
            'quehuo' => 0,
            'jushou' => 0,
            'shifa' => 0
        ];
        //缺货,拒收,实发,百分比 需要根据实际发货计算
        foreach ($storeGoods as $storeId => $goodsList) {
            $orderPayment = $orderPaymentDefault;
            foreach ($goodsList as $goods) {
                $goodsPayment = $goods['payments'];
                $orderPayment['quehuo'] += ($goods['goods_price'] * $goodsPayment['quehuo_number']);
                $orderPayment['jushou'] += $goods['goods_price'] * $goodsPayment['jushou_number'];
                $orderPayment['shifa'] += $goodsPayment['shifa_amount'];
            }
            $orderPayment['percent'] = round($orderPayment['shifa']/$mainOrderPayments['shifa'], 4) * 100;
            $orderPayment['store_id'] = $storeId;

            $orderPayments[$storeId] = $orderPayment;
        }
        //修正百分比
        if (100 != collect($orderPayments)->sum('percent')) {
            $orderPayments = fixArrayTotal($orderPayments, ['percent'], 100);
            $orderPayments = collect($orderPayments)->keyBy('store_id')->toArray();
        }
        //keys by store_id
        //子单字段对应主单的内容
        $subOrderDefault = [
            'qiandan'      => $mainOrderPayments['qiandan'],
            'ziti'         => $mainOrderPayments['ziti'],
            'qita'         => $mainOrderPayments['qita'],
            'weicha'       => $mainOrderPayments['weicha'],
            'yingshou'     => $mainOrderPayments['yingshou'],
            'pos'          => $mainOrderPayments['pos'],
            'weixin'       => $mainOrderPayments['weixin'],
            'alipay'       => $mainOrderPayments['alipay'],
            'yizhifu'      => $mainOrderPayments['yizhifu'],
            'cash'         => $mainOrderPayments['cash'],
            'shishou'      => $mainOrderPayments['shishou'],
            'delivery_fee' => $mainOrderPayments['delivery_fee'],
            'driver_fee'   => $mainOrderPayments['driver_fee'],
        ];

        //金额根据百分比分摊
        $storeOrderMap = $this->getStoreOrderMap($mainOrderPayments['pay_id']);
        foreach ($orderPayments as $storeId => $subPayments) {
            $percentage = $subPayments['percent'] / 100;
            foreach ($subOrderDefault as $key => $value) {
                $subPayments[$key] = round($value * $percentage, 4);
            }
            $condition = [
                'pay_id'   => $subPayments['pay_id'],
                'store_id' => $storeId,
            ];

            //冗余数据
            $subPayments['store_id'] = $storeId;
            $subPayments['order_id'] = $storeOrderMap[$storeId]['order_id'];
            $subPayments['order_sn'] = $storeOrderMap[$storeId]['order_sn'];
            //数据写入或则更新
            $result = $this->subOrderPayment->updateOrInsert($condition, $subPayments);
            if ($result === false) {
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

            DB::beginTransaction();
            try {
                $this->orderGoodsPayment->updateDeliveryFee($payId);
                $this->mainOrderPayment->updateDeliveryFee($payId);
                $this->subOrderPayment->updateDeliveryFee($payId);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->fail('重算失败：'.$e->getMessage());
            }
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
        $result = $this->mainOrderPayment->jizhang($payIds);
        if ($result) {
            return $this->success('记账成功');
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
        $result = app(\App\Services\MainOrderPaymentsExport::class)->excel($request->toArray());
        dd($result);
    }
}