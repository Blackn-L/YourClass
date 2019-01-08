<?php

namespace app\api\validate;

use think\Validate;

class Login extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
        'email' => 'require|email',
        'password' => 'require|min:8|max:30|regex:/^(?![^a-zA-Z]+$)(?!\D+$)/'
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
        'password.require' => '密码不能为空',
        'password.min' => '密码不能小于8个字符',
        'password.max' => '密码不能大于30个字符',
        'password.regex' => '密码必须包含数字和字母'
    ];
}
