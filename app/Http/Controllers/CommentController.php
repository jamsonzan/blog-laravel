<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $which = $request->get('which');
        if($which != 'user') {
            $which = 'article';
        }
        $comments = $this->getComments($which, $id);
        return response()->json(array(
            'code' => 1,
            'len' => $comments->count(),
            'data' => $comments
        ));
    }

    private function getComments($which, $id)
    {
        return Comment::where($which.'_id', '=', $id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(($uid = $request->post('uid'))
            &&($article_id = $request->post('article_id'))
            &&($comment_ = $request->post('comment'))
            &&($nickname = $request->post('nickname'))
        ){
            try {
                $comment = new Comment;
                $comment->user_id = $uid;
                $comment->article_id = $article_id;
                $comment->comment = $comment_;
                $comment->nickname = $nickname;
                $comment->save();
                return response()->json(array(
                    'code' => 1,
                    'data' => $comment->id
                ));
            }catch (\Exception $e){
                return response()->json(array(
                    'code' => 0,
                    'message' => '用户id或文章id不正确'
                ));
            }
        }else{
            return response()->json(array(
                'code' => 0,
                'message' => '文章表单不完整'
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            Comment::where('id', '=', $id)
                ->where('user_id', '=', $request->all('uid'))->delete();
        }catch (\Exception $e){
            return response()->json(array(
                'code' => 0,
                'message' => 'uid或评论id不正确'
            ));
        }
        return response()->json(array(
            'code' => 1,
            'message' => '删除成功'
        ));
    }
}
