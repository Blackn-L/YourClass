<?php

namespace app\api\controller;
use think\Controller;
use think\captcha\Captcha;

class GetCaptcha extends Controller
{
    public function verify() {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    public function check($code) {
        $captcha = new Captcha();
        if($captcha->check($code)) {
            return JsonData(200, true, '验证码校验正确');
        } else {
            return JsonData(400, false, '验证码校验错误');
        }
    }
}