<?php

namespace app\api\validate;

use think\Validate;

class Register extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'email' => 'require|email',
        'username' => 'require|min:3|max:25|chsAlphaNum',
        'password' => 'require|min:8|max:25|regex:/^(?=.*\d)(?=.*[a-zA-Z]).{8,25}$/',
        'emailCode' => 'require'
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式不正确',
        'username.require' => '用户名不能为空',
        'username.min' => '用户名不能小于3个字符',
        'username.max' => '用户名不能大于25个字符',
        'username.chsAlphaNum' => '用户名只能输入中文，英文，数字以及下划线',
        'password.require' => '密码不能为空',
        'password.min' => '密码不能小于5个字符',
        'password.max' => '密码不能大于25个字符',
        'password.regex' => '密码必须包含字母和数字，不能使用特殊字符',
        'emailCode.require' => '邮箱验证码不能未空'
    ];
}
