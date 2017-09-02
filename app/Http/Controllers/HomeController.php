<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Store;
use Illuminate\Http\Request;

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
        parent::__construct();
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
}
