<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PictureController extends Controller
{
    public function store(Request $request)
    {
        $file = $request->file('file');
        if($file->isValid()){
            $hashName = $file->hashName();
            $file->move('images', $hashName);
            return response()->json(array('code'=>1, 'url'=>'http://'.$request->getHttpHost().'/images/'.$hashName));
        }else{
            return response()->json(array('code'=>0, 'message'=>'file is not valid'));
        }
    }

    public function delete(Request $request)
    {
        //$imgSrc = $request->all('imgSrc');
        //$url = parse_url($imgSrc);
        //unlink(substr($url['path'], 1));
        return response()->json(array(
            'code' => 1,
            'message' => '删除成功'
        ));
    }
}
