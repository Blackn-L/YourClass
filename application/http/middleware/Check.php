<?php

namespace app\http\middleware;
use think\facade\Session;
class Check
{
    public function handle($request, \Closure $next)
    {
        if (Session::has('uid')) {
            return $next($request);
        }
        return JsonData('301',null,'您还未登陆，请先登陆');
    }
}
