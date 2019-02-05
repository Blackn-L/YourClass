<?php

namespace app\http\middleware;
use think\facade\Session;
class CheckIsAdmin
{
    public function handle($request, \Closure $next)
    {
        $uid = Session::get('uid');
        $flag = UserModel::where('uid', $uid)->value('super_admin_flag');
        if ($flag == 1) {
            $request->isAdmin = true;
        } else {
            $request->isAdmin = false;
        }
        return $next($request);
    }
}
