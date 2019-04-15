<?php
namespace App\Http\Controllers\Trait_;
use App\User_article_type;

trait CheckType
{
    private $has_this_type = true;
    private $uid;
    private $type;

    private function goodType($uid, $type)
    {
        $this->uid = $uid;
        $this->type = $type;

        if($type == ''){
            return $this->has_this_type;
        } elseif(!is_string($type)
            ||!User_article_type::where('user_id', '=', $uid)->where('type', '=', $type)->count()
        ){
            $this->has_this_type = false;
        }
        return $this->has_this_type;
    }

    private function badType()
    {
        return response()->json(array(
            'code' => 0,
            'message' => "用户$this->uid"."没有$this->type"."类型的文章"
        ));
    }
}
/**
 * Created by PhpStorm.
 * User: jamsonzan
 * Date: 2019/4/14
 * Time: 17:29
 */