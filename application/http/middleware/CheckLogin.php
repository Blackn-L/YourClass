<?php

namespace app\http\middleware;
use think\facade\Session;
class CheckLogin
{
    public function handle($request, \Closure $next)
    {
        if (Session::has('uid')) {
            $request->isLogin = true;
        } else {
            $request->isLogin = false;
        }
        return $next($request);
    }
}
