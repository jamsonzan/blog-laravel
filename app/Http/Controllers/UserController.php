<?php

namespace App\Http\Controllers;

use App\Article;
use App\Http\Controllers\Trait_\CheckType;
use App\Http\Controllers\Trait_\CheckUid;
use App\Http\Controllers\Trait_\CheckNull;
use App\User;
use App\User_article_type;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class UserController extends Controller
{
    use CheckUid, CheckNull, CheckType;

    public function login(Request $request)
    {
        $name = $request->post('name');
        $password = $request->post('password');
        $taken = str_random(50);

        if(empty($name) || empty($password)){
            return response()->json(array(
                'code' => 0,
                'message' => '用户命或密码不能为空'
            ));
        }
        $password = sha1('QQ'.$password);
        $user = User::where('name', '=', $name)->where('password', '=', $password)->first();
        if($user){
            $user->taken = $taken;
            $user->save();
            $cookie = new Cookie('taken', $taken);
            return response()->json(array(
                'code' => 1,
                'data' => $user->id
            ))->withCookie($cookie);
        }else{
            if($this->hasName($name)){
                return response()->json(array(
                    'code' => 0,
                    'message' => '用户已存在但密码不正确'
                ));
            }else{
                return $this->register($request, $name, $password, $taken);
            }
        }
    }

    public function register($request, $name, $password, $taken)
    {
        $user = new User;
        $user->name = $name;
        $user->password = $password;
        $user->taken = $taken;
        $user->save();
        //初始化u_a_types表
        User_article_type::insert(array(
            'user_id' => $user->id,
            'type' => 'type1'
        ));
        return $this->login($request);
    }

    public function getSetting(Request $request)
    {
        if(($uid = $request->get('uid')) && $this->goodUid($request->get('uid'))
        ||($uid = $this->getIdByName($request->get('name')))
        ||($uid = $this->getIdByTaken($request->cookie('taken')))
        ){
            $user = User::where('id', '=', $uid)->get(array('id', 'name', 'signature'))->first();
            $types = User_article_type::where('user_id', '=', $uid)->get(array('type'));

            return response()->json(array(
                'code' => 1,
                'data' => array(
                    'user' => $user,
                    'types' => $types
                )
            ));
        }else{
            return response()->json(array(
                'code' => 0,
                'message' => '没有提供用户信息或用户信息不正确'
            ), 400);
        }
    }

    public function setSetting(Request $request)
    {
        $result = $this->setUser($request);
        if($result['code'] == 0){
            return $result;
        }
        $result = $this->setType($request);
        if($result['code'] == 0){
            return $result;
        }

        return array(
            'code' => 1,
            'message' => '修改成功'
        );
    }

    private function setUser(Request $request)
    {
        $uid = $request->post('uid');

        if($name = $request->post('name')){
            if($this->hasName($name)){
                    return array(
                        'code' => 0,
                        'message' => '用户名'.$name.'已经被抢啦'.'，再换一个叭~'
                    );
            }else{
                    $user = User::find($uid);
                    $user->name = $name;
                    $user->save();
                }
        }

        if($password = $request->post('password')){
            $password = sha1('QQ'.$password);
            $user = User::find($uid);
            $user->password = $password;
            $user->save();
        }

        if($s = $request->post('signature')){
            $user = User::find($uid);
            $user->signature = $s;
            $user->save();
        }

        return array('code' => 1,);
    }

    private function setType(Request $request)
    {
        $uid = $request->post('uid');
        //如果输入null，空串，0值就等于没输入
        if($type = $request->post('type')){
            //如果用户没有该类型则插入
            if(!$this->goodType($uid, $type)){
                $addType = new User_article_type;
                $addType->user_id = $uid;
                $addType->type = $type;
                $addType->save();
            }else{
                $articleNum = Article::where('user_id', '=', $uid)
                    ->where('type', '=', $type)->count();
                //如果有该类型文章则不能删除该类型
                if($articleNum){
                    return array('code' => 0, 'message' => '存在该类型的文章，不能删除哦~~');
                }
                User_article_type::where('user_id', '=', $uid)
                    ->where('type', '=', $type)->delete();
            }
        };
        return array('code' => 1);
    }

    private function hasName($name)
    {
        $user = User::where('name', '=', $name)->first();
        $hasName = $user? true : false;
        return $hasName;
    }

    public function getIdByName($name)
    {
        return $this->getId('name', $name);
    }

    public function getIdByTaken($taken)
    {
        return $this->getId('taken', $taken);
    }

    private function getId($key, $value)
    {
        if(empty($value)){
            return false;
        }else{
            $user = User::where($key, '=', $value)->get(array('id'));
        }
        if($user->count()){
            return $user->first()->id;
        }
        return false;
    }
}
