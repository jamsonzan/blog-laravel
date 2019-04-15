<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use App\Deleted_article;
use App\Http\Controllers\Trait_\CheckType;
use App\Http\Controllers\Trait_\CheckUid;
use App\Http\Controllers\Trait_\ServerBusy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    use CheckUid, CheckType, ServerBusy;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $uid = $request->get('uid');
        $type = $request->get('type');
        if(!$this->goodUid($uid)){
            return $this->badUid();
        } elseif (!$this->goodType($uid, $type)){
            return $this->badType();
        };

        $type = $type? $type : '%';
        try {
            $articles = Article::where('user_id', '=', $uid)
                ->where('type', 'like', $type)->get();
        } catch (\Exception $e){
            return $this->tooBusy();
        }

        return response()->json(array(
            'code' => 1,
            'len' => $articles->count(),
            'data' => $articles
        ));
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
    public function store(Request $request, $update=false)
    {
        if(($uid = $request->post('uid'))
        &&($type = $request->post('type'))
        &&($head = $request->post('head'))
        &&($body = $request->post('body'))
        &&($public = $request->post('public'))
        ){
            if(!$this->goodType($uid, $type)){
                return $this->badType();
            }
            if($update){
                $article = Article::where('id', '=', $update)
                    ->where('user_id', '=', $uid)->first();
                $article = $article->count()? $article : new Article;
            }else{
                $article = new Article;
            }

            $article->user_id = $uid;
            $article->type = $type;
            $article->head = $head;
            $article->body = $body;
            $article->public = $public;
            $article->save();
            return response()->json(array(
                'code' => 1,
                'data' => $article->id
            ));
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
     * @retun \Illuminate\Http\Respons
     */
    public function show($id)
    {
        try {
            $article = Article::where('id', '=', $id)->first();
        } catch (\Exception $e){
            return $this->tooBusy();
        }
        if(!$article){
            return array(
               'code' => 0,
               'message' => "没有这篇文章"
            );
        }
        return array(
            'code' => 1,
            'len' => $article->count(),
            'data' => $article
        );
    }

    public function detail($id)
    {
        $result = $this->show($id);
        if($result['code']==1){
            $article = $result['data'];
            return view('articlepage', compact('article'));
        }else{
            return $result;
        }
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
        return $this->store($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $article = Article::where('id', '=', $id)
            ->where('user_id', '=', $request->all('uid'));

        DB::transaction(function () use ($article, $id) {
                Comment::where('article_id', '=', $id)->delete();
                Deleted_article::insert($article->first()->toArray());
                $article->delete();
            }
        );
        return response()->json(array(
            'code' => 1,
            'message' => '删除成功'
        ));
    }

}
