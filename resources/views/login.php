<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录博客后台</title>
    <link rel="icon" href="../favicon.ico"/>
    <!-- 引入 Bootstrap.css -->
    <link rel="stylesheet" href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .form-login{
            max-width: 330px;
            padding: 15px;
            margin: 115px auto;
        }
        .form-head{text-align: center;}
        #password{margin-top: 5px;}
        .btn{margin-top: 10px;}
    </style>
</head>
<body style="padding: 2%">

<form class="form-login">
    <h2 class="form-head">Please Login For Management</h2>
    <input type="text" class="form-control" placeholder="Username" name="username" id="username" autofocus>
    <input type="password" class="form-control" placeholder="Password" name="password" id="password">
    <button class="btn btn-sm btn-primary btn-block" type="submit" id="button">Login</button>
</form>

<div class="footer" style="text-align: center;margin-top: 80px;border-top: 1px solid #f5f5f5;height: 40px"><small>created by jamsonzan</small></div>
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
    $('#button').click(function (event) {
        event.preventDefault();
        let name = $('#username').val();
        let password = $('#password').val();
        $.post('/api/login',{
            name: name,
            password: password
        }, function (data) {
            console.log(data.code);
            if (data.code==1){
                window.location.href = '/admin?uid='+data.data
            }else {
                alert(data.message)
            }}
        )
    });
</script>
</body>
</html>