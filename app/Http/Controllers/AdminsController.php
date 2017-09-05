<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;

class AdminsController extends Controller
{
    /**
     * @var Admin
     */
    private $admin;

    public function __construct(Admin $admin)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->admin = $admin;
    }

	public function index()
	{
		$admins = Admin::paginate();
		return view('admins.index', compact('admins'));
		return response($admins);
	}

    public function list()
    {
        return $this->admin->where('admin_id', 'admin_name')->get();
	}

    public function show(Admin $admin)
    {
        response(compact('admin'));
    }

	public function store(AdminRequest $request)
	{
		$admin = Admin::create($request->all());
	  return response(['id'=>$admin->id, 'message'=>'Created successfully.']);
	}

	public function update(AdminRequest $request, Admin $admin)
	{
		$this->authorize('update', $admin);
		$admin->update($request->all());
	  return response(['id'=>$admin->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(Admin $admin)
	{
		$this->authorize('destroy', $admin);
		$admin->delete();

    response(['message' => 'Deleted successfully.']);
  }
}