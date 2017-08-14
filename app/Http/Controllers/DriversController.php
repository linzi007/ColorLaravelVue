<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;

class DriversController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index()
	{
		$drivers = Driver::paginate();

        return response($drivers);
	}

    public function show(Driver $driver)
    {
        return response(compact('driver'));
    }

	public function store(DriverRequest $request)
	{
		$driver = Driver::create($request->all());

        return response(['id'=>$driver->id, 'message'=>'Created successfully.']);
	}

	public function update(DriverRequest $request, Driver $driver)
	{
		$this->authorize('update', $driver);
		$driver->update($request->all());

        return response(['id'=>$driver->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(Driver $driver)
	{
		$this->authorize('destroy', $driver);
		$driver->delete();

        response(['message' => 'Deleted successfully.']);
	}
}