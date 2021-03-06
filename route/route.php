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
Route::post('api/register', 'api/User/register');
// 发送邮件
Route::post('api/getEmailCode', 'api/User/sendEmail');
// 登陆
Route::post('api/login', 'api/User/login');
// 登出
Route::get('api/loginOut', 'api/User/loginOut');
// 判断邮箱是否已经注册
Route::post('api/checkEmail', 'api/User/checkEmailIsReg');
// 校验邮箱验证码，发送新密码至邮箱
Route::post('api/checkCode', 'api/User/checkCode');
// 获取用户信息
Route::get('api/getUserInfo', 'api/User/getInfo');
// 更新用户信息
Route::post('api/updateUserInfo', 'api/User/updateInfo');
// 校验密码
Route::post('api/checkPwd', 'api/User/checkPwd');
// 更新密码
Route::post('api/updatePwd', 'api/User/updatePwd');
// 更新教务信息
Route::post('api/updateStuInfo', 'api/User/updateStuInfo');
// 获取验证码
Route::get('api/getCaptcha/verify','api/GetCaptcha/verify');
// 校验验证码
Route::get('api/getCaptcha/check/:code','api/GetCaptcha/check');
// 获取课程表列表
Route::post('api/getClassList','api/MainClass/getClassList');
// 获取学生成绩
Route::post('api/getStuMark', 'api/MainClass/getStuMark');
return [

];
