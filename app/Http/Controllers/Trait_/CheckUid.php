<?php
namespace App\Http\Controllers\Trait_;
use App\User;

trait CheckUid
{
    private $has_uid = true;
    private $has_this_uid = true;

    private function goodUid($uid)
    {
        if(empty($uid) && $uid != 0){
            $this->has_uid = false;
            return false;
        }
        if(!is_numeric($uid)
        ||!User::where('id', '=', $uid)->count()
        ){
            $this->has_this_uid = false;
            return false;
        }
        return true;
    }

    private function badUid()
    {
            if(!$this->has_uid){
                return response()->json(array(
                    'code' => 0,
                    'message' => '必须提供uid'
                ), 400);
            }else{
                return response()->json(array(
                    'code' => 0,
                    'message' => '不存在该uid'
                ), 400);
            }
    }
}
/**
 * Created by PhpStorm.
 * User: jamsonzan
 * Date: 2019/4/14
 * Time: 16:57
 */