<?php


namespace App\Services;


use App\Models\ExchangeBottle;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ExchangeBottlesExport
{
    /**
     * @var ExchangeBottle
     */
    private $exchangeBottle;

    const CHUNK_SIZE = 5000;

    /**
     * ExchangeBottlesExport constructor.
     */
    public function __construct(ExchangeBottle $exchangeBottle)
    {

        $this->exchangeBottle = $exchangeBottle;
    }

    public function excel($params)
    {
        $condition = [];
        if (!empty($params['created_at']) && 'null' != $params['created_at'][0]) {
            $startAt = Carbon::parse($params['created_at'][0])->toDateTimeString();
            $endAt = Carbon::parse($params['created_at'][1])->toDateTimeString();
            $this->exchangeBottle = $this->exchangeBottle->whereBetween('created_at', [$startAt, $endAt]);
        }

        if (!empty($params['store_id'])) {
            $condition['store_id'] = $params['store_id'];
        }

        if (!empty($params['pay_sn'])) {
            $condition['pay_sn'] = $params['pay_sn'];
        }
        getSql();
        $data = $this->exchangeBottle->where($condition);
        $count = $data->count();
        dd($count);
        return Excel::create($this->getFileName(), function ($excel) use ($data) {
            $data->chunk($this->getChunkSize(), function ($items) use ($excel) {
                $collection = $this->transformCollection($items);
                $excel->sheet('sheet1', function ($sheet) use ($collection) {
                    $sheet->fromModel($collection, null, 'A1', true);
                });
            });
        })->export('xls');
    }

    /**
     * 导出表的名称
     * @return string
     */
    public function getFileName()
    {
        return '换盖金额汇总表' . Carbon::now();
    }

    public function getChunkSize()
    {
        return self::CHUNK_SIZE ? self::CHUNK_SIZE : 5000;
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    private function transformCollection($collection)
    {
        $collection = $collection->toArray();
        $adminIds = $driverIds = [];
        foreach ($collection as $item) {
            $driverIds[] = $item['driver_id'];
            $adminIds[] = $item['creator'];
        }
        $drivers = $admins = [];
        if ($driverIds = array_filter($driverIds)) {
            $drivers = app(\App\Models\Driver::class)->select('id', 'name')->whereIn('id', $driverIds)->get()->toArray();
        }
        if ($adminIds = array_filter($adminIds)) {
            $admins = app(\App\Models\Admin::class)->select('admin_id', 'admin_name')->whereIn('admin_id', $adminIds)->get()->toArray();
        }
        $drivers = array_column($drivers, 'name', 'id');
        $admins = array_column($admins, 'admin_name', 'admin_id');
        $stores = app(\App\Models\Store::class)->getStoreCache();
        $stores = array_column($stores, 'store_name', 'store_id');
        $exportData = [];
        foreach ($collection as $payment) {
            $exportData[] = [
                '登记时间'   => $payment['created_at'],
                '档口'   => empty($stores[$payment['store_id']]) ? '' : $stores[$payment['store_id']],
                '换盖金额'   => $payment['amount'],
                '经办司机'    => empty($payment['driver_id']) ? '' : $drivers[$payment['driver_id']],
                '关联单号'    => $payment['pay_sn'],
                '操作员'    => empty($admins[$payment['creator']]) ? '' : $admins[$payment['creator']],
            ];
        }
        return $exportData;
    }
}