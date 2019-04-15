<?php
namespace App\Http\Controllers\Trait_;
use Illuminate\Http\Request;

trait CheckNull{
    private function goodInput(Request $request, array $arrs)
    {
        foreach ($arrs as $a){
            if(!$request->has($a)){
                return false;
            }
            if(!$request->all('a')){
                return false;
            }
        }
    }

    private function badInput()
    {
        return array(
            'code' => 0,
            'message' => '输入存在空值'
        );
    }
}
/**
 * Created by PhpStorm.
 * User: jamsonzan
 * Date: 2019/4/15
 * Time: 9:36
 */