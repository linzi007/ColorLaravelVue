<?php


namespace App\Http\Middleware;



use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class VueParams extends TransformsRequest
{
    const SORT_ORDER_MAP = [
        'ascending' => 'asc', 'descending' => 'desc',
    ];

    /**
     * VUE 请求的部分字段统一处理
     *
     * @param string $key
     * @param mixed $value
     * @return array|mixed|string
     */
    protected function transform($key, $value)
    {
        if ('sort_order' == $key) {
            if (in_array($value, ['ascending', 'descending'])) {
                return self::SORT_ORDER_MAP[$value];
            }
        }

        return $value;

    }

}
