<?php

namespace app\api\controller;
use think\captcha\Captcha;

class GetVerify
{
    public function verify() {
        $captcha = new Captcha();
        return $captcha->entry();
    }
}