<?php

namespace app\api\controller;
use think\facade\Session;
use app\api\model\User as UserModel;
class GetUserInfo
{
    public function getInfo() {
        $check = new CheckUser();
        if (!$check->isLogin()) {
            return JsonData(400,false, '请登陆！');
        }
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        unset($user['password']);
        unset($user['super_admin_flag']);
        unset($user['password']);
        unset($user['delete_flag']);
        unset($user['jw_student_pwd']);
        unset($user['jw_cookies']);
        return JsonData(200, $user, '获取用户信息成功！');
    }
}