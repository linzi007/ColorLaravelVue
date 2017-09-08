<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ExcelTrait;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use Maatwebsite\Excel\Facades\Excel;

class DriversController extends Controller
{
    use ExcelTrait;
    /**
     * @var Driver
     */
    private $driver;

    const Export_FIELDS = [
        'code' => '编号',
        'name' => '名称',
        'mobile' => '手机',
        'description' => '备注',
    ];
    public function __construct(Driver $driver)
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->driver = $driver;
    }

	public function index(Request $request)
	{
        $where = $this->getWhere($request);
		$drivers = $this->driver->where($where)
            ->orderBy('id', 'asc')->paginate();
        return response($drivers);
	}

    public function list(Request $request)
    {
        $name = $request->get('name');
        $drivers = $this->driver->select(['id', 'name'])->where('name', 'like', $name . '%')
            ->orderBy('id', 'desc')->get();
        return response($drivers);
    }

    public function show(Driver $driver)
    {
        return response(compact('driver'));
    }

	public function store(DriverRequest $request)
	{
		$driver = $this->driver->create($request->all());

        return response(['id'=>$driver->id, 'message'=>'Created successfully.']);
	}

	public function update(DriverRequest $request, Driver $driver)
	{
		$driver->update($request->all());

        return response(['id'=>$driver->id, 'message'=>'Updated successfully.']);
	}

	public function destroy(Driver $driver)
	{
		$this->authorize('destroy', $driver);
		$driver->delete();

        response(['message' => 'Deleted successfully.']);
	}

    public function import(Request $request)
    {
        $sheet = Excel::selectSheetsByIndex(0)->load(storage_path('tel.xlsx'))->get();
        $driver = $this->driver;
        $sheet->each(function ($row) use($driver) {
            $driver->updateOrInsert(['code' => $row->code], $row->toArray());
        });

        return response('success');
    }

    public function export(Request $request)
    {
        $header = array_values(self::Export_FIELDS);
        $drivers = $this->driver->where($this->getWhere($request))
            ->get(array_keys(self::Export_FIELDS))->toArray();
        $this->exportExcel($drivers, $header, '司机信息');
    }

    private function getWhere($params)
    {
        $where = [];
        if (empty($params)) {
            return $where;
        }
        if ($params->code) {
            $where['code'] = $params->code;
        }
        if ($params->name) {
            $where['name'] = $params->name;
        }
        if ($params->mobile) {
            $where['mobile'] = $params->mobile;
        }
        if ($params->description) {
            $where['description'] = $params->description;
        }

        return $where;
	}
}