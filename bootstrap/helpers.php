<?php
/**
 * 打印sql
 */
if (!function_exists('getSql')) {
    function getSql()
    {
        if (env('APP_ENV') === 'local') {
            DB::listen(function ($sql) {
                dump($sql);
                $singleSql = $sql->sql;
                if ($sql->bindings) {
                    foreach ($sql->bindings as $replace) {
                        $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                        $singleSql = preg_replace('/\?/', $value, $singleSql, 1);
                    }
                    dump($singleSql);
                } else {
                    dump($singleSql);
                }
            });
        }
    }
}

function currentUserId()
{
    return auth()->id();
}

/**
 * 修正价格查出合计值
 *
 * @param array $array
 * @param mixed $field
 * @param number $total
 * @return array
 */
function fixArrayTotal($array, $fields, $total)
{
    if (!is_array($fields)) {
        if (strpos($fields, ',')) {
            $fields = explode(',', $fields);
        } else {
            $fields[] = $fields;
        }
    }

    $array = collect($array);
    $lastItem = $array->pop();
    foreach ($fields as $field) {
        $sumAmount = $array->sum($field);
        if ($sumAmount > $total) {
            $lastItem[$field] = 0;
        } else {
            $lastItem[$field] = $total - $sumAmount;
        }
    }
    $array->push($lastItem);
    return $array->toArray();
}