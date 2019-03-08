<?php

namespace app\api\controller;
use think\Controller;
use app\api\model\User as UserModel;
use app\api\model\Classes as ClassModel;
use think\facade\Session;
use think\Request;

class MainClass extends Controller
{
    // 中间件 判断是否登陆
    protected $middleware = ['CheckLogin'];

    // 获取课程表列表
    public function getClassList(Request $request) {
        if (!$request->isLogin) {
            return JsonData(300, false, '请先登陆！');
        }
        $uid = Session::get('uid');
//        $user = Session::get('user_info');
        $user = UserModel::get($uid);
        # 账号密码错误
        if (!$user['jw_student_id'] && !$user['jw_student_pwd']) {
            return JsonData(400, false, '教务账号密码错误！');
        }
        # 无cookies则去登陆获取
        if (!$user['jw_cookies']) {
            $username = $user['jw_student_id'];
            $password = $user['jw_student_pwd'];
            $loginUrl = 'http://127.0.0.1:8080/flask/api/login';
            $loginUrl = $loginUrl.'/'.$username.'/'.$password;
            $data = url_get($loginUrl);
            if (!$data) {
                return JsonData(400, false, '爬虫系统暂未运行');
            }
            $user['jw_cookies'] = $data['Data'];
            $user->save();
            Session::clear();
            Session::set('uid', $user->uid);
            Session::set('user_info',$user);
        }
        # 获取学生ID，存入数据库
        if (!$user['student_id']) {
            $getStuIdUrl = 'http://127.0.0.1:8080/flask/api/getstuid/'.$user['jw_cookies'];
            $stuId = url_get($getStuIdUrl);
            if ($stuId['Code'] == 200) {
                $user['student_id'] = $stuId['Data'];
                $user->save();
            }
        }
        $getClassUrl = 'http://127.0.0.1:8080/flask/api/getclasslist/'.$user['jw_cookies'];
        $res = url_get($getClassUrl);
        // 250 为cookie错误，再次尝试登陆
        if ($res['Code'] == 250) {
            $username = '201540704357';
            $password = 'ss44520f';
            $loginUrl = 'http://127.0.0.1:8080/flask/api/login';
            $loginUrl = $loginUrl.'/'.$username.'/'.$password;
            $data = url_get($loginUrl);
            if (!$user['jw_cookies']) {
                return JsonData(400, false, '爬虫系统暂未运行');
            }
            $user['jw_cookies'] = $data['Data'];
            $user->save();
            Session::clear();
            Session::set('uid', $user->uid);
            Session::set('user_info',$user);
            $getClassUrl = 'http://127.0.0.1:8080/flask/api/getclasslist/'.$user['jw_cookies'];
            $res = url_get($getClassUrl);
        }
        // 将课程信息，学期，学年等存入数据库
//        $classInfo = ClassModel::
        return $res;
    }
}