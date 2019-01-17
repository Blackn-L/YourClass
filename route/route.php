<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});
// 注册
Route::post('api/register', 'api/Register/index');
// 发送邮件
Route::post('api/getEmailCode', 'api/Register/sendEmail');
// 登陆
Route::post('api/login', 'api/Login/index');
Route::get('hello/:name', 'index/hello');

Route::get('api/getcaptcha','api/GetCaptcha/verify');
return [

];
