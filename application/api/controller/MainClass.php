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
                return JsonData(400, false, '登陆失败！');
            }
        }
        # 获取学生ID，存入数据库
        $stuId = $user['student_id'];
        if (!$user['student_id']) {
            $stuId = $this->getStuId($uid);
        }
        # 获取课程表
        $jwClass = $this->getClass($jwCookie, $data['yearId'], $data['termId'], $uid, $stuId);
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
    public function checkCookie($jwCookie,$uid) {
        // 如果过期，则重新登陆,返回正确cookie
        $currentCookie = $jwCookie;
        $checkUrl = 'http://127.0.0.1:8080/flask/api/checkcookie/'.$currentCookie;
        $res = url_get($checkUrl);
        if ($res['Code'] == 250) {
            $currentCookie = $this->toLogin($uid);
            if (!$jwCookie) {
                return false;
            }
        }
        return $currentCookie;
    }
    // 获取课程表
    public function getClass($jwCookie, $yearId, $termId, $uid, $stuId) {
        $oneId = intval(strval($uid).$yearId.$termId);
        $classInfo = ClassModel::where('one_id', $oneId)->find();
        // 课程表已存在
        if (!!$classInfo['class_info']) {
            $info = json_decode($classInfo['class_info'], true);
            return JsonData(200, $info, '课程信息获取成功');
        }
        // 数据库无此课程表
        // 获取有效的Cookie
        $currentCookie = $this->checkCookie($jwCookie, $uid);
        $getClassUrl = 'http://127.0.0.1:8080/flask/api/getclasslist/'.$currentCookie.'/'.$yearId.'/'.$termId.'/'.$stuId;
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
            return JsonData(400, false, '课程信息获取失败');
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
            return $user['student_id'];
        }
    }
    // 获取学生成绩
    public function getStuMark(Request $request) {
        //  前端传值，学年和学期
        if (!$request->isLogin) {
            return JsonData(300, false, '请先登陆！');
        }
        $data = $this->request->param();
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        $oneId = intval(strval($uid).$data['yearId'].$data['termId']);
        $classInfo = ClassModel::where('one_id', $oneId)->find();
        if (!!$classInfo['marks'] && $classInfo['marks'] != '[]') {
            $info = json_decode($classInfo['marks'], true);
            return JsonData(200, $info, '课程成绩成功');
        }
        $jwCookie = $user['jw_cookies'];
        // 获取有效的Cookie
        $currentCookie = $this->checkCookie($jwCookie, $uid);
        $getMarkUrl = 'http://127.0.0.1:8080/flask/api/getmark/'.$currentCookie.'/'.$data['yearId'].'/'.$data['termId'];
        $res = url_get($getMarkUrl);
        // 是更新还是新增
        if (!$classInfo) {
            // 新增则实例化模型
            // ThinkPHP根据条件自动判断是新增还是更新
            $classInfo = new ClassModel;
        }
        if ($res['Code'] == 200) {
            // 将课程信息，学期，学年等存入数据库
            $classInfo['one_id'] = $oneId;
            $classInfo['uid'] = $uid;
            $classInfo['year_id'] = $data['yearId'];
            $classInfo['term_id'] = $data['termId'];
            $classInfo['marks'] = json_encode($res['Data'], JSON_UNESCAPED_UNICODE); // 将数组转成json格式存入数据库,且不转为unicode
            $result = $classInfo->allowField(true)->save($classInfo);
            return $res;
        } else {
            return JsonData(400, false, '课程成绩获取失败');
        }
    }
}