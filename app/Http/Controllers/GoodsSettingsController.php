<?php

namespace App\Http\Controllers;

use App\Models\GoodsSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GoodsSettingRequest;

class GoodsSettingsController extends Controller
{
    /**
     * 货品配送费设置表
     * GoodsSettingsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index()
	{
		$goods_settings = GoodsSetting::paginate();

		return response($goods_settings);
	}

	public function store(GoodsSettingRequest $request)
	{
		$goods_setting = GoodsSetting::create($request->all());
	    return response(['id'=>$goods_setting->id, 'message'=>'Created successfully.']);
	}

	public function update(GoodsSettingRequest $request, GoodsSetting $goods_setting)
	{
		$this->authorize('update', $goods_setting);
		$goods_setting->update($request->all());
	    return response(['id'=>$goods_setting->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(GoodsSetting $goods_setting)
	{
		$this->authorize('destroy', $goods_setting);
		$goods_setting->delete();

        response(['message' => 'Deleted successfully.']);
    }
}