<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>博客后台管理</title>
    <link rel="icon" href="../favicon.ico"/>
    <!--  Bootstrap.css, summernote.css -->
    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.css" rel="stylesheet">
    <style>
        .success{
            position: absolute;
            display: none;
            text-align: center;
            top: 180px;
            width: 100%;
            font-size: 17px;
            z-index: 10;
        }
        .table-container{margin-top: 50px;}
        table{max-height: 30px}
        tr{max-height: 30px}
        td {
            height: 30px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .content{margin: 50px 5%;}
        #head, #type, #submit{margin-top: 20px}
        #submit{width: 150px;margin: 20px auto 0 auto}
        .sub{margin: 0 auto}
    </style>
</head>
<body style="padding: 2%">
<div id="container">
    <div class="page-header">
        <span id="uid" style="display: none"></span>
        <h3><span id="name"></span>的个人博客<br><small id="signature"></small></h3>
    </div>
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="#" id="article">article</a></li>
        <li role="presentation"><a href="#" id="comment">comment</a></li>
        <li role="presentation"><a href="#" id="publish">publish blog</a></li>
        <li role="presentation"><a href="#" id="setting">setting</a></li>
    </ul>

    <div class="content"></div>

    <div class="footer" style="text-align: center;margin-top: 80px;border-top: 1px solid #f5f5f5;height: 40px"><small>created by jamsonzan</small></div>
</div>

<div class="success">成功登录啦！</div>
<!-- jquery, summernote,bs.js -->
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote.js"></script>
<script>
    //获取用户信息
    let user = null;
    let types = null;
    let uid = getUrlParam('uid');
    getSetting();
    function getSetting(){
        $.ajax({
            method: 'get',
            url: '/api/users/setting?uid='+uid,
            async: false,
            success: function (data) {
                user = data.data.user;
                types = data.data.types;
                $('#name').html(user.name);
                $('#signature').text(user.signature);
            },
            error: function (e) {
                alert(e.responseText);
            }
        });
    }

    console.log(user.name);

    //解析url
    function getUrlParam(name) {
        let reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        let r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]); return null; //返回参数值
    }
    //显示文章模块
    $(document).ready(function () {
        showarticles();
    });
    $('#article').click(function () {
        $('.content').val('');
        showarticles();
    });
    function showarticles() {
            $.get('api/articles/index?uid='+uid, function (data) {
                if(data.len >= 1){
                    $('.content').html('    <div class="table-container">' +
                        '    <table class="table table-hover table-bordered">' +
                        '        <tr><th>id</th><th>type</th><th>head</th><th>body_len</th><th>public</th><th>created_at</th><th>update_at</th><th>operate</th></tr>' +
                        '    </table></div>');
                    data.data.forEach(function (v) {
                        $('.table').append(get_article_body(v));
                    });
                    bindoperate();
                }else {
                    $('.content').html('<div style="text-align: center;margin-top: 50px">还没有文章哦O(∩_∩)O</div>');
                }
            });
    }

    function get_article_body(row){
            row_html = '<tr><td>'+row.id+'</td><td>'+row.type+'</td><td>'+row.head+'</td><td>'+row.body.length+
                '</td>'+'<td>'+row.public+'</td><td>'+row.created_at+'</td><td>'+row.updated_at+'</td><td>';
            row_html +=  '     <a href="#" class="btn btn-primary btn-xs update" id="'+row.id+'">update</a>'+
                '     <a href="/articles/'+row.id+'" class="btn btn-info btn-xs" target="_blank">detail</a>'+
                '     <a href="#" class="btn btn-danger btn-xs delete" id="'+row.id+'">delete</a>'+
                '</td></tr>';
            return row_html;
    }
    //显示评论
    $('#comment').click(function () {
        $.get('api/comments/'+uid+'?which=user', function (data) {
            console.log(uid);
            console.log(data.len);
            if (data.len >= 1) {
                $('.content').html(
                    '    <div class="table-container">' +
                    '    <table class="table table-hover table-bordered">' +
                    '        <tr><th>id</th><th>article_id</th><th>comment</th><th>nickname</th><th>readed</th><th>created_at</th><th>updated_at</th><th>operate</th></tr>' +
                    '    </table></div>'
                );
                data.data.forEach(function (v) {
                    $('.table').append(get_comment_tbody(v));
                });
                bind_comment_del();
            } else {
                $('.content').html('<div style="text-align: center;margin-top: 50px">还没有评论哦O(∩_∩)O</div>');
            }
        })
    });

        function get_comment_tbody(row) {
            row_html = '<tr><td>' + row.id + '</td><td>' + row.article_id + '</td><td>' +
                row.comment.replace(/[<>"&\/`']/g, '').slice(0, 15) + '</td><td>' + row.nickname.replace(/[<>"&\/`']/g, '') +
                '</td>' + '<td>' + row.readed + '</td><td>' + row.created_at + '</td><td>' + row.updated_at + '</td><td>';
            row_html += '<a href="#" class="btn btn-danger btn-xs delete" id="' + row.id + '">delete</a>' +
                '</td></tr>';
            return row_html;
        }

        //文章点击事件
        function bindoperate() {
            //更新文章
            $('.update').click(function (e) {
                e.preventDefault();
                let id = $(this).attr('id');
                console.log(id);
                $.ajax({
                    type: 'get',
                    url: 'api/articles/' + id,
                    success: function (data) {
                        if (data.code == 1) {
                            $('#publish').click();
                            let article = data.data;
                            $('#head').val(article.head);
                            $("#type").val(article.type);
                            $('#summernote').summernote('code', article.body);
                            $('.form-article').prepend('<input style="display: none" id="update" value="' + id + '">');
                            $('#submit').text('modify');
                            initsubmit('api/articles/'+id);
                        } else {
                            console.log(data.message);
                        }
                    }
                });
            });
            //删除文章
            $('.delete').click(function (e) {
                e.preventDefault();
                let id = $(this).attr('id');
                if (confirm("确定要删除id为" + id + "的文章吗？")) {
                    $.ajax({
                        type: 'delete',
                        url: 'api/articles/' + id,
                        data: {
                            uid: uid
                        },
                        success: function (data) {
                            if (data.code == 1) {
                                alert('删除成功啦');
                                $('#article').click();
                            } else {
                                alert('删除失败啦,message：'+data.message);
                                console.log(data.message);
                            }
                        },
                        error: function () {
                            alert('网络出了点问题哦（＞人＜；）');
                        }
                    })
                }
            })
        }

    //绑定删除评论
    function bind_comment_del(){
        console.log('begin_bind');
        $('.delete').click(function (e) {
            e.preventDefault();
            let id = $(this).attr('id');
            if(confirm("确定要删除id为"+id+"的评论吗？")){
                $.ajax({
                    type: 'delete',
                    url: 'api/comments/'+id,
                    data:{
                        uid: uid
                    },
                    success: function (data) {
                        if(data.code==1) {
                            alert('删除成功啦');
                            $('#comment').click();
                        }else {
                            alert('删除失败啦,message:'+data.message);
                            console.log(data.message);
                        }
                    },
                    error: function () {
                        alert('网络出了点问题哦（＞人＜；）');
                    }
                })
            }
        })
    }

    //发布文章表单
    $('#publish').click(function () {
        $('.content').html('<form class="form-article" style="width: 70%;margin: 0 auto">' +
            '<h2 style="text-align: center">Publish an Article</h2>' +
            '<input type="text" class="form-control" placeholder="Title" id="head" required autofocus>' +
            '<select class="form-control" id="type"></select>' +
            '<textarea id="summernote" name="body"></textarea>' +
            '<div style="text-align: center"><button class="btn btn-sm btn-primary" type="submit" id="submit">Submit</button></div></form>');
        types.forEach(function (v) {
                $('#type').append('<option value="'+v.type+'">'+v.type+'</option>')
        });
        initsummernote();
        initsubmit();
    });
    //初始化summernote并监听图片上传事件

    function initsummernote() {
        $('#summernote').summernote({
                toolbar: [

                    <!--字体工具-->

                    ['fontname', ['fontname', 'fontsize']], //字体系列

                    ['style', ['bold', 'italic', 'underline', 'clear']], // 字体粗体、字体斜体、字体下划线、字体格式清除

                //    ['font', ['strikethrough', 'superscript', 'subscript']], //字体划线、字体上标、字体下标

                    ['fontsize', ['fontsize']], //字体大小
                    ['height', ['height']], //行高

                    ['color', ['color']], //字体颜色

                    <!--段落工具-->

                    ['style', ['style']],//样式

                  //  ['para', ['ul', 'ol', 'paragraph']], //无序列表、有序列表、段落对齐方式

                    <!--插入工具-->

                    ['table',['table']], //插入表格

                    ['hr',['hr']],//插入水平线

                    ['link',['link']], //插入链接

                    ['picture',['picture']], //插入图片

                  //  ['video',['video']], //插入视频

                    <!--其它-->

                    ['fullscreen',['fullscreen']], //全屏

                    ['codeview',['codeview']], //查看html代码

                    ['undo',['undo']], //撤销

                    ['redo',['redo']], //取消撤销

                 //   ['help',['help']], //帮助

                ],
            height: 550,
            marginTop: 20,
            focus: false,
            placeholder: 'Write Here...',
            required: true,
            callbacks: {
                onImageUpload: function (files) {
                    let formData = new FormData();
                    formData.append('file', files[0]);
                    formData.append('uid', uid);
                    $.ajax({
                        url: 'api/pictures',//上传图片文件处理地址,
                        type: "POST",
                        data: formData,
                        dataType: 'JSON',
                        processData: false,//告诉jQuery不要加工数据
                        contentType: false,
                        success: function (data) {
                            $('#summernote').summernote('insertImage', data.url);
                        },
                        error: function () {
                            alert('添加图片失败啦( ╯□╰ )');
                        }
                    });
                },
                onMediaDelete: function (target) {
                    console.log(target);
                    let imgSrc = target.context.currentSrc;
                    $.ajax({
                        type: 'delete',
                        url: 'api/pictures/1',
                        data:{
                            uid: uid
                        },
                        success: function (data) {
                            console.log(data);
                        }
                    });
                }
            }
        });
    }
    //绑定提交文章按钮
    function initsubmit(url='api/articles') {
        $('#submit').click(function (event) {
            event.preventDefault();
            let head = $('#head').val();
            let type = $('#type').val();
            let body = $('#summernote').summernote('code');
            //article_id ,更新时yong
            let update = $('#update').val();
            console.log(head, type, body, update);
            let method = update ? 'put' : 'post';
            $.ajax({
                type: method,
                url: url,
                data: {
                    head: head,
                    type: type,
                    body: body,
                    uid: uid,
                    public: 1
                },
                success: function (data) {
                    if (data.code == 1) {
                        $('#summernote').summernote('code', '');
                        $('#title').val('');
                        $('.success').html('提交成功啦O(∩_∩)O').show().fadeOut(3000);
                        $('#article').click();
                    } else {
                        alert(data.message+'，检查一下标题或正文叭~~');
                    }
                }
            });
        });
    }
    //setting
    $('#setting').click(function () {
        $('.content').html('<div style="width: 60%;margin: 0px auto">'+
            '<form class="form-login">'+
            '    <h3 class="form-head" style="text-align: center;margin-top: 15%">My Articles Type</h3>'+
            '    <input type="text" class="form-control" placeholder="Type To Operate" id="ttype">'+
            '    <button class="btn btn-sm btn-primary btn-block" type="submit" id="ADtype">Add Or Delete</button>'+
            '</form>'+
            '<form class="form-login">'+
            '    <h3 class="form-head" style="text-align: center;margin-top: 20%">Name Or Signature Or Password</h3>'+
            '    <input type="text" class="form-control" placeholder="'+user.name+'" id="nname">'+
            '    <input type="text" class="form-control" placeholder="'+user.signature+'" id="s">'+
            '    <input type="password" class="form-control" placeholder="Password" id="new">'+
            '    <button class="btn btn-sm btn-primary btn-block" type="submit" id="Update">Update</button>'+
            '</form>'+
                '</div>'
        );
        $('#Update').click(function (event) {
            event.preventDefault();
            let name = $('#nname').val();
            let s = $('#s').val();
            let new_ = $('#new').val();
            console.log(user);
            if(name == user.name){
                name = '';
                console.log(name+s+new_);
            }
            $.ajax({
                type: 'post',
                url: 'api/users/setting',
                data: {
                    uid: uid,
                    name: name,
                    signature: s,
                    password: new_
                },
                success: function (data) {
                    if(data.code == 1){
                        getSetting();
                        $('#nname').val('');
                        $('#s').val('');
                        $('#new').val('');
                        $('.success').html('Update成功啦O(∩_∩)O').show().fadeOut(2500);
                    }else {
                        alert(data.message);
                    }
                }
            });
        });

        $('#ADtype').click(function (event) {
            event.preventDefault();
            let type = $('#ttype').val();

            $.ajax({
                type: 'post',
                url: 'api/users/setting',
                data: {
                    uid: uid,
                    type: type
                },
                success: function (data) {
                    if(data.code == 1){
                        $('#ttype').val('');
                        console.log(data.message);
                        getSetting();
                        $('.success').html('Add Or Delete成功啦O(∩_∩)O').show().fadeOut(2500);
                    }else {
                        alert(data.message);
                    }
                }
            });
        });
    });

    //导航状态管理
    $(".nav a").click( function(){
            $(".nav").find(".active").removeClass("active");
            $(this).parent().addClass("active");
    });
    //登录成功提示
    $('.success').html('登录成功啦O(∩_∩)O').show().fadeOut(2500);
</script>
</body>
</html>