<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use Maatwebsite\Excel\Facades\Excel;

class StoresController extends Controller
{
    /**
     * @var Store
     */
    private $stores;

    public function __construct(Store $stores)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->stores = $stores;
    }

	public function list(Request $request)
	{
        return Cache::remember('stores', 60*6, function () use ($request) {
            $stores = app(\App\Models\Store::class)->whereIn('store_state', [0, 1])
                ->orderBy('store_state', 'desc');
            if($request->get('name')){
                $stores->where('store_name', 'like', '%' . $request->get('name') . '%');
            }
            return $stores->get();
        });
    }
}