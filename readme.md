项目后端本来是用lumen做的，微框架虽然小巧快捷，但是
毕竟是laravel脱胎出来的，少了很多实用功能，社区和
生态也没有laravel完善，所以决定用laravel重写。效果
可以浏览我的blog：www.jamsonzan.xyz/jamsonzan

这是一个多用户blog，想使用它很简单，访问我的blog点击导航栏的admin，
直接接登录就行了，是的，不用注册，你登录时的账号密码就是用
于管理你博客的账号密码。你的博客首页为
jamsonzan.xyz/{你登录时的账号名}

当然，你也可以clone这个项目然后自己搭服务器，
怎么搭服务器以及一些配置就不说了，下载安装这个项目：
```
git clone https://github.com/jamsonzan/blog-laravel.git
```
然后进入blog-laravel目录:
```
composer install
```
稍等片刻就安装好啦！
