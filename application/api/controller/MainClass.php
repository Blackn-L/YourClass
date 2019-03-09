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
        //  前端传值，学年和学期
        $data = $this->request->param();
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        # 账号密码错误
        if (!$user['jw_student_id'] && !$user['jw_student_pwd']) {
            return JsonData(400, false, '教务账号密码错误！');
        }
        # 无cookies则去登陆获取
        $jwCookie = $user['jw_cookies'];
        if (!$jwCookie) {
            $jwCookie = $this->toLogin($uid);
            if (!$jwCookie) {
                JsonData(400, false, '登陆失败！');
            }
        }
        # 获取学生ID，存入数据库
        if (!$user['student_id']) {
            $this->getStuId($uid);
        }
        # 获取课程表
        $jwClass = $this->getClass($jwCookie, $data['yearId'], $data['termId'], $uid);
        return $jwClass;

    }
    // 登陆
    public function toLogin($uid) {
        $user = UserModel::get($uid);
        $username = $user['jw_student_id'];
        $password = $user['jw_student_pwd'];
        $loginUrl = 'http://127.0.0.1:8080/flask/api/login';
        $loginUrl = $loginUrl.'/'.$username.'/'.$password;
        $data = url_get($loginUrl);
        if ($data['Code'] === 200) {
            $user['jw_cookies'] = $data['Data'];
            $user->save();
            Session::clear();
            Session::set('uid', $user->uid);
            Session::set('user_info',$user);
            return $data['Data'];
        } else {
            return false;
        }
    }
    // 判断cookie是否过期
    public function checkCookie($jwCookie) {
        // 如果过期，则返回false，重新登陆
        // 如果没过期，则返回true
    }
    // 获取课程表
    public function getClass($jwCookie, $yearId, $termId, $uid) {
        $oneId = intval(strval($uid).$yearId.$termId);
        $classInfo = ClassModel::where('one_id', $oneId)->find();
        // 课程表已存在
        if (!!$classInfo['class_info']) {
            $info = json_decode($classInfo['class_info'], true);
            return JsonData(200, $info, '课程信息获取成功');
        }
        // 数据库无此课程表
        $getClassUrl = 'http://127.0.0.1:8080/flask/api/getclasslist/'.$jwCookie.'/'.$yearId.'/'.$termId;
        $res = url_get($getClassUrl);
        if ($res['Code'] == 200) {
            // 将课程信息，学期，学年等存入数据库
            $newClass = new ClassModel;
            $newClass['one_id'] = $oneId;
            $newClass['uid'] = $uid;
            $newClass['year_id'] = $yearId;
            $newClass['term_id'] = $termId;
            $newClass['class_info'] = json_encode($res['Data'], JSON_UNESCAPED_UNICODE); // 将数组转成json格式存入数据库,且不转为unicode
            $status = $newClass->allowField(true)->save($newClass);
            return $res;
        } else {
            return JsonData(400, $res['Code'], '课程信息获取失败');
        }

    }

    // 获取学生ID
    public function getStuId($uid) {
        $user = UserModel::get($uid);
        $getStuIdUrl = 'http://127.0.0.1:8080/flask/api/getstuid/'.$user['jw_cookies'];
        $stuId = url_get($getStuIdUrl);
        if ($stuId['Code'] == 200) {
            $user['student_id'] = $stuId['Data'];
            $user->save();
        }
    }
    // 获取学生成绩
    public function getStuGrade($uid) {
        $user = UserModel::get($uid);
    }
}