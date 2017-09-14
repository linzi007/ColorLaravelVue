<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * @var Store
     */
    private $store;
    /**
     * @var Driver
     */
    private $driver;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Store $store, Driver $driver)
    {
        $this->middleware('auth');
        $this->store = $store;
        $this->driver = $driver;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $request = request();
        $stores = $this->store->list($request);
        $drivers = $this->driver->select(['id', 'name'])->orderBy('id', 'desc')->get();
        return view('welcome', compact('stores', 'drivers'));
    }

    public function userInfo()
    {
        if ($user = Auth::user()) {
            $user = $user->toArray();
            $user['role'] = ['admin'];
            $user['avatar'] = 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif';
        }
        return $user;
    }
}
