<?php

namespace App\Http\Controllers;

use App\Models\Store;
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
	    $storeName = $request->get('name');
		$stores = $this->stores->select(['store_id', 'store_name'])->whereIn('store_state', [0, 1])
            ->orderBy('store_state', 'desc');
		if($storeName){
            $stores->where('store_name', 'like', '%' . $storeName . '%');
        }

        $stores = $stores->get();
        return response($stores);
	}
}