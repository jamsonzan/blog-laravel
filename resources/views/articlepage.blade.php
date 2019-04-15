<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="icon" href="../favicon.ico"/>
    <!-- 引入 Bootstrap.css -->
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//ku.shouce.ren/libs/lightbox/2.7.1/css/lightbox.css">
    <style>
        #container{
            border:1px solid white;
            word-wrap: break-word;
        }
        .content{
            padding-top: 40px;
            overflow: hidden;
            border-bottom: 1px solid #f5f5f5;
        }
        .comment-md{
            position: fixed;
            top: 130px;right: 10px;
            border: 1px solid #f5f5f5;
            border-radius: 8px;
            width: 30%;
            height: 70%;
            padding: 3%;
            margin-left: 70%;

        }
        .comment-sm{padding: 10%}
        .comment{
            margin-top: 2%;
            border-bottom: 1px solid aliceblue;
        }
        .submit-success{
            position: fixed;
            display: none;
            text-align: center;
            bottom: 390px;
            width: 100%;
            font-size: 17px;
            z-index: 10;
        }
        #toTop, #bgc{
            position: fixed;
            left: 66%;
            bottom: 20%;
            width: 25px;
            height: 25px;
            padding: 5px;
            padding-top: 2px;
            border: 1px solid #f5f5f5;
        }
        #bgc{bottom: 25%}
    </style>
</head>
<body style="padding: 2%">
<canvas id="canvas" style="display: block;width: 100%;height: 100%;position: fixed;z-index: -1"></canvas>
    <div class="page-header">
        <div class="page-header">
            <span id="uid" style="display: none">{{ $article->user_id }}</span>
            <span id="id" style="display: none">{{$article->id}}</span>
            <h3><span id="name"></span>的个人博客<br><small id="signature"></small></h3>
        </div>
    </div>
    <div id="container">
        <div class="row" style="width: 100%">
            <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9">
            <div class="content">
                <h3 class="text-center">{{$article->head}}</h3>
                <div class="other" style="margin-top: 40px;color: lightgray"><small style="border-bottom: 1px solid #f5f5f5">time: {{$article->created_at}}</small></div>
                <div class="body" style="margin-top: 40px;padding: 5%;font-size: 1.3em;line-height: 1em">
                {!! $article->body !!}
                </div>
            </div>
            </div>
        </div>
            <div class="comment-md">
                <h6 class="text-center">comment</h6>
                <div class="comment-group pre-scrollable" style="height: 50%"></div>
                <div class="to-comment" style="padding: 10%;margin-top: 10%; border-top: 1px solid lightskyblue">
                    <form>
                        <input type="text" class="form-control input-sm" placeholder="nickname" style="height: 5%" id="nickname" required autofocus>
                        <textarea type="text" class="form-control input-sm" rows="2" placeholder="comment" style="height: 10%" id="comment" required></textarea>
                        <button type="submit" class="btn btn-xs btn-primary btn-block" style="height: 5%" id="submit">commit</button>
                    </form>
                </div>
            </div>
    </div>
<a id="show_img" data-lightbox="group" style="display: none"></a>
<div class="submit-success"></div>
<a href="#" id="toTop"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a>
<a href="#" id="bgc"><span class="glyphicon glyphicon-adjust" aria-hidden="true"></span></a>
<div class="footer" style="text-align: center;margin-top: 80px;border-top: 1px solid #f5f5f5;height: 40px"><small>created by jamsonzan</small></div>
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script src="../js/canvas.js"></script>
<script src="//ku.shouce.ren/libs/lightbox/2.7.1/js/lightbox.min.js"></script>
<script>
    //获取用户信息
    let uid = $('#uid').text();
    $(function () {
        $.ajax({
            method: 'get',
            url: '/api/users/setting?uid='+uid,
            async: false,
            success: function (data) {
                let user = data.data.user;
                $('#name').html(user.name);
                $('title').html(user.name+'的博客全文');
                $('#signature').text(user.signature);
            },
            error: function (e) {
                alert(e.responseText);
            }
        });
    });
    //点击图片放大
    $('img').click(function () {
        let src = $(this).attr('src');
        $('#show_img').attr('href', src).click();
    });

    //获取文章所有评论
    $(function () {
        let id = $('#id').text();
        //'http://'+window.location.host+'/
        $.get('http://'+window.location.host+'/api/comments/'+id+'?which=article',function (data) {
            if(data.len >= 1){
                data.data.forEach(function (v) {
                    $('.comment-group').append(
                        '<div class="comment">'+
                        '<span style="font-family: Microsoft YaHei UI Light"> '+v.nickname.replace(/[<>"&\/`']/g, '')+
                        ':&nbsp;&nbsp;&nbsp;</span>'+v.comment.replace(/[<>"&\/`']/g, '')+
                        '</div>'
                    );
                });
            }else {
                $('.comment-group').append(
                    '<div>快来坐沙发叭，嘿嘿~~</div>'
                );
            }
        });
    });
    //提交评论并更新到页面上
    $('#submit').click(function (e) {
        e.preventDefault();
        let id = $('#id').text();
        let uid = $('#uid').text();
        let nickname = $('#nickname').val();
        let comment = $('#comment').val();
        $.post('http://'+window.location.host+'/api/comments',{
            article_id: id,
            uid: uid,
            nickname: nickname,
            comment: comment
            },function (data) {
                if(data.code==1){
                    $('.submit-success').text('提交成功啦(*^_^*)').show().fadeOut(2500);
                    $('.comment-group').append(
                        '<div class="comment">'+
                        '<strong>'+nickname.replace(/[<>"&\/`']/g, '')+':</strong>'+ comment.replace(/[<>"&\/`']/g, '')+
                        '</div>'
                    );
                    console.log(comment);
                    $('#nickname,#email,#comment').val('');
                }else {
                    alert('提交失败哦＞﹏＜，message：'+data.message)
                }
            }
        );
    })
    //继承父窗口颜色//无效＞︿＜
    let bgc = window.parent.sbgc;
    $('body').css('background-color', window.parent.sbgc);
    //切换背景颜色
    $('#bgc').click(function (e) {
        e.preventDefault();
        let bgc = $('body').css('background-color');
        let new_bgc = bgc=='rgb(255, 255, 255)'? 'aliceblue' : 'white';
        if(new_bgc=='white'){
            $('.submit-success').text('白色是永远的时尚^_~').show().fadeOut(1500);
        }else {
            $('.submit-success').text('蓝色是淡淡的忧伤^_~').show().fadeOut(1500);
        }
        $('body').css('background-color', new_bgc);
    });
    //当设备宽度小于768px时，放置评论栏于文章后面
    let width = window.screen.width;
    console.log(width);
    let num = 60;
    if(width <= 768){
        $('.comment-md').removeClass('comment-md').addClass('comment-sm');
        $('#toTop, #bgc').css('left', '90%');
        num = 26;
    }
    window.addEventListener('load', init(num));

</script>
</body>
</html>
