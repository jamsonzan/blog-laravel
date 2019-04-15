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
        #toTop, #bgc{
            position: fixed;
            left: 90%;
            bottom: 20%;
            width: 25px;
            height: 25px;
            padding: 5px;
            padding-top: 2px;
            border: 1px solid #f5f5f5;
        }
        #bgc{bottom: 25%;}
        .submit-success{
            position: fixed;
            display: none;
            text-align: center;
            bottom: 390px;
            width: 100%;
            font-size: 17px;
            z-index: 10;
        }
    </style>
</head>
<body id="bd" style="padding: 3%;overflow-x: hidden;background-color: white">
<canvas id="canvas" style="display: block;width: 95%;height: 100%;position: fixed;z-index: -1"></canvas>
<div id="container">
    <div class="page-header">
        <span id="uid" style="display: none"></span>
        <h3><span id="name"></span>的个人博客<br><small id="signature"></small></h3>
    </div>
    <ul class="nav nav-tabs" style="opacity: 0.7">
        <li role="presentation" class="active"><a href="#">home</a></li>
    </ul>

    <div class="content">
        <div class="row" style="margin-top: 70px;width: 100%;"></div>
    </div>

    <div class="submit-success"></div>
    <a href="#container" id="toTop"><span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a>
    <a href="#" id="bgc"><span class="glyphicon glyphicon-adjust" aria-hidden="true"></span></a>
    <a id="show_img" data-lightbox="group" style="display: none"></a>
    <div class="footer" style="text-align: center;margin-top: 80px;border-top: 1px solid #f5f5f5;height: 40px"><small>created by jamsonzan</small></div>
</div>
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script src="js/canvas.js"></script>
<script>
    //获得用户信息，name，sign，types等
    let name = window.location.pathname.slice(1);
    name = name? name : 'jamsonzan';
    $(function () {
        $.ajax({
           method: 'get',
           url: '/api/users/setting?name='+name,
           async: false,
           success: function (data) {
               let user = data.data.user;
               let types = data.data.types;
               $('#uid').html(user.id);
               $('#name').html(user.name);
               $('title').html(user.name+'的个人博客');
               $('#signature').text(user.signature);
               types.forEach(function (v) {
                   $('.nav').append('<li role="presentation"><a href="#">' + v.type + '</a></li>');
               });
               bindNav();
           },
           error: function (e) {
               alert(e.responseText);
           }
        });
    });
        //home
        $(function () {
        get_data_and_show('home');
        });
        function get_data_and_show(type='home') {
        type = type=='home' ? '' : type;
        $.get('api/articles/index?uid='+$('#uid').text()+'&type='+type, function (data) {
            if(data.len >= 1) {
                data.data.forEach(function (v) {
                    $('.row').append(get_nail_html(v));
                });
            }else {
                $('.row').append('<div style="text-align: center;margin-top: 50px">还没有文章哦O(∩_∩)O</div>');
            }
        }).fail(function () {
            alert("连接服务器失败(ノへ￣、)")
        });
        }
        function get_nail_html(article){
        let row_html = '<div class="col-xs-11 col-sm-4 col-xs-offset-1">'+'<a href="/articles/'+article.id+'" style="color: black;text-decoration:none" target="_blank">'+
            '<div class="thumbnail" style="background-color:rgba(255,255,255,0.6); overflow: hidden">'+
            '<div class="caption">';
        row_html += '<h3 style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis">'+article.head+'</h3>'+
            '<div class="body" style="height: 200px; overflow: hidden; padding: 5%">' + article.body+'</div>'+
            '<p style="margin-top: 30px">' +
            '<a href="/articles/'+article.id+'" class="btn btn-primary detail" role="button" target="_blank">Detail</a> ' +
            '<a href="/articles/'+article.id+'" class="btn btn-default comment" role="button" target="_blank">Comment</a>' +
            '</p>'+
            '</div>'+
            '</div>'+'</a>'+
            '</div>';
        return row_html;
        }

    //导航切换
    function bindNav() {
        $(".nav a").click(function () {
            $(".nav").find(".active").removeClass("active");
            $(this).parent().addClass("active");
            $('.row').html('');
            get_data_and_show($(this).text());
        });
    }
    //切换背景颜色
    $('#bgc').click(function (e) {
        e.preventDefault();
        let bgc = $('body').css('background-color');
        let new_bgc = bgc=='rgb(255, 255, 255)'? 'aliceblue' : 'white';
        $('body').css('background-color', new_bgc);
        $('#sbgc').html(new_bgc);
        
        if(new_bgc=='white'){
            $('.submit-success').text('白色是永远的时尚^_~').show().fadeOut(1500);
        }else {
            $('.submit-success').text('蓝色是淡淡的忧伤^_~').show().fadeOut(1500);
        }
    });

    //当设备小于768px
    let width = window.screen.width;
    let num = width <= 768? 26 : 60;
    window.addEventListener('load', init(num));
</script>
</body>
</html>
