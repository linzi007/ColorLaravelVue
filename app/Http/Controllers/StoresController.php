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
        return $this->stores->list($request);
    }
}