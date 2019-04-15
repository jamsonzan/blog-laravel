<?php
/**
 * Created by PhpStorm.
 * User: jamsonzan
 * Date: 2019/4/14
 * Time: 18:22
 */

namespace App\Http\Controllers\Trait_;


trait ServerBusy
{
    private function tooBusy()
    {
        return response()->json(array(
            'code' => 0,
            'message' => '服务器繁忙，请稍后重试'
        ), 503);
    }
}