<?php

namespace app\api\controller;
use think\facade\Session;
use app\api\model\User as UserModel;
class CheckUser
{
    // 判断用户是否登陆
    public function isLogin() {
        $uid = Session::get('uid');
        $user = Session::get('user_info');
        // uid不存在
        if (!$uid || !$user) {
            return false;
        }
        // uid与用户信息中的uid不相等
        if ($uid != $user->uid) {
            return false;
        }
        return true;
    }

    //  判断是否是管理员
    public function isAdmin() {
        $uid = Session::get('uid');
        $flag = UserModel::where('uid', $uid)->value('super_admin_flag');
        if ($flag != 1) {
            return false;
        }
        return true;
    }
}