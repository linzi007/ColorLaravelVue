<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Imports\GoodsSettingImport;
use App\Http\Controllers\Traits\ExcelTrait;
use App\Models\Goods;
use App\Models\GoodsSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoodsSettingRequest;
use Maatwebsite\Excel\Facades\Excel;

class GoodsSettingsController extends Controller
{
    use ExcelTrait;
    /**
     * @var GoodsSetting
     */
    private $goodsSetting;
    /**
     * @var Goods
     */
    private $goods;

    private $storagePath = 'exports';

    /**
     * 货品配送费设置表
     * GoodsSettingsController constructor.
     */
    public function __construct(GoodsSetting $goodsSetting, Goods $goods)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->goodsSetting = $goodsSetting;
        $this->goods = $goods;
    }

	public function index(Request $request)
	{
        $sortOrderMap = [
            'ascending' => 'asc', 'descending' => 'desc',
        ];
        $sortBy = 'goods_id';
        $sortOrder = 'desc';
        if ($request->sort_by && in_array($request->sort_by, ['goods_id', 'store_id'])) {
            $sortBy = 'goods.'.$request->sort_by;
        }
        if ($request->sort_order && in_array($request->sort_order, ['ascending', 'descending'])) {
            $sortOrder = $sortOrderMap[$request->sort_order];
        }

        $where = [];
        if ($request->store_id) {
            $where['goods.store_id'] = $request->store_id;
        }
        if ($request->has('shipping_charging_type')) {
            $where['goods_settings.shipping_charging_type'] = $request->shipping_charging_type;
        }
        if ($request->goods_name) {
            $where['goods.goods_name'] = ['like', $request->goods_name.'%'];
        }
        if ($request->goods_serial) {
            $where['goods.goods_serial'] = $request->goods_serial;
        }
        if ($request->goods_id) {
            $where['goods.goods_id'] = $request->goods_id;
        }
        $stores = app(\App\Models\Store::class)->getStoreCache();
        $stores = array_column($stores, 'store_name', 'store_id');

        $goodsSettings = $this->goods->list($where)->orderBy($sortBy, $sortOrder)->paginate($request->per_page)->toArray();
        foreach ($goodsSettings['data'] as $key => $payment) {
            $payment['store_name'] = empty($stores[$payment['store_id']]) ? $payment['store_id'] : $stores[$payment['store_id']];
            $goodsSettings['data'][$key] = $payment;
        }

        return response()->json($goodsSettings);
	}

    public function searchByRelations(Request $request)
    {
        if ($request->store_id) {
            $where['store_id'] = $request->store_id;
        }
        if ($request->has('shipping_charging_type')) {
            $where['shipping_charging_type'] = $request->shipping_charging_type;
        }
        if ($request->has(['goods_name', 'goods_serial'])) {
            if ($request->goods_name) {
                $condition['goods_name'] = ['like', $request->goods_name];
            }
            if ($request->goods_serial) {
                $condition['goods_serial'] = $request->goods_serial;
            }
        }
        if ($request->goods_id) {
            $where['goods_id'] = $request->goods_id;
        } else if (!empty($condition)) {
            $goods = $this->goods->where($condition)->first();
            if (!empty($goods)) {
                $where['goods_id'] = $goods['goods_id'];
            }
        }
        $goodsSettings = $this->goodsSetting->with(['goods' => function($query) {
            $query->select(['goods_serial, goods_name, goods_price, g_unit']);
        }])->paginate($request->per_page);

        foreach ($goodsSettings['data'] as $key => $item) {
            if (!empty($item['goods'])) {
                $payment = $item['goods'];
                unset($item['goods']);
                $mainOrders['data'][$key] = array_merge($item, $payment);
            }
        }
        return response()->json($goodsSettings);
	}

	public function store(GoodsSettingRequest $request)
	{
        $data = $request->all();
        $insert = [
            'store_id' => $data['store_id'],
            'goods_id' => $data['goods_id'],
            'shipping_charging_type' => $data['shipping_charging_type'],
            'shipping_rate' => $data['shipping_rate'],
            'unpack_fee' => $data['unpack_fee'],
            'driver_charging_type' => $data['driver_charging_type'],
            'driver_rate' => $data['driver_rate'],
        ];
		$goods_setting = $this->goodsSetting->updateOrInsert(['goods_id' => $insert['goods_id']], $insert);
        if ($goods_setting) {
            return $this->success('保存成功');
        }

        return $this->fail('保存失败');
	}

	public function update(GoodsSettingRequest $request, GoodsSetting $goods_setting)
	{
		//$this->authorize('update', $goods_setting);
        $data = $request->all();
        $insert = [
            'store_id' => $data['store_id'],
            'goods_id' => $data['goods_id'],
            'shipping_charging_type' => $data['shipping_charging_type'],
            'shipping_rate' => $data['shipping_rate'],
            'unpack_fee' => $data['unpack_fee'],
            'driver_charging_type' => $data['driver_charging_type'],
            'driver_rate' => $data['driver_rate'],
        ];
        $goods_setting = GoodsSetting::create($insert);
        if ($goods_setting) {
            return $this->success('保存成功');
        }
	}

    public function import(GoodsSettingImport $import)
    {
        $result = $import->doImport();
        if ($result['status']) {
            return $this->success('导入成功');
        }
        $name = $result['data']['file'];
        $path = DIRECTORY_SEPARATOR . $this->storagePath . DIRECTORY_SEPARATOR;
        return $this->fail('部分导入失败', ['path' => $path, 'name' =>$name]);
    }

    public function export(Request $request)
    {
        return app('App\Services\GoodsSettingExport')->excel($request->toArray());
	}

    /**
     * 错误下载
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFile(Request $request)
    {
        $name = $request->name;
        $path = $request->path . $name;

        return response()->download(storage_path($path), $name);
    }
}