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
}