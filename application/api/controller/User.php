<?php
namespace app\api\controller;
use think\Controller;
use app\api\model\User as UserModel;
use think\facade\Session;
use think\Request;

class User extends Controller
{
    // 中间件 判断是否登陆
    protected $middleware = [
        'CheckLogin' => [ 'except' => ['register', 'login', 'loginOut', 'sendEmail'] ],
    ];
    // 注册
    public function register()
    {
        $data = $this->request->param();
        $validate = new \app\api\validate\Register;
        if (!$validate->check($data)) {
            return JsonData(400,false,$validate->getError());
        }
        $trueEmailCode = Session::get('emailCode');
        if ($data['emailCode'] != $trueEmailCode) {
            return JsonData(400,false,'邮箱验证码错误！');
        }
        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $isEmail = UserModel::where('email', $email)->find();
        if ($isEmail != null) {
            return JsonData(400,false,'当前邮箱已注册！');
        };
        $isUsername = UserModel::where('username', $username)->find();
        if ($isUsername != null) {
            return JsonData(400, false, "当前用户名已存在！");
        }
        $newUser = new UserModel;
        $newUser['email'] = $email;
        $newUser['username'] = $username;
        $newUser['password'] = md5($password);
        $status = $newUser->allowField(true)->save($newUser);
        if ($status) {
            // 注册成功后清空session中的正确code，防止一个code重复注册
            Session::delete('emailCode');
            return JsonData(200,true,"注册成功！");

        };
        return JsonData(400,false,"系统运行错误!");
    }
    // 登陆
    public function login() {
        $data = $this->request->param();
        $validate = new \app\api\validate\Login;
        if (!$validate->check($data)) {
            return JsonData(400,false, $validate->getError());
        }
        // 判断验证码是否正确
        if (!captcha_check($data['code'])) {
            return JsonData(400,false,'验证码错误');
        }
        $email = $data['email'];
        // 后期考虑邮箱和用户名都可以登录
        $user = UserModel::where('email', $email)->find();
        if (!$user) {
            return JsonData(400,false,'当前邮箱尚未注册');
        }
        $password = md5(trim($data['password']));
        if ($user->password != $password) {
            return JsonData(400,false,'密码错误');
        }
        // 删除原本session
        Session::clear();
        // 获取Session会话 ID
        Session::set('uid', $user->uid);
        Session::set('user_info',$user);
        // 保存此次登陆Ip
        $user['last_login_ip'] = $this->request->ip();
        $user->save();
        return JsonData(200,true,'登陆成功');
    }
    // 登出
    public function loginOut() {
        Session::clear();
        return JsonData(200, true, '登出成功！');
    }
    // 发送邮箱验证码
    public function sendEmail() {
        $title = '测试邮件';
        $toEmail = '893637294@qq.com';
        $code = mt_rand(100000,999999);
        $name = '测试用户';
        $body = '您的验证码是：'.$code;
        $result=send_mail($toEmail, $name, $title, $body);
        if($result){
            //记录邮件验证码
            session('emailCode',$code);
            return JsonData(200,true,'发送成功！');
        }else{
            return JsonData(400,false,'发送失败！');
        }
    }
    // 获取用户信息
    public function getInfo(Request $request) {
        if (!$request->isLogin) {
            return JsonData(300, null, '请先登陆！');
        }
        $uid = Session::get('uid');
//        $user = Session::get('user_info');
        $user = UserModel::get($uid);
        $info = [];
        $info['uid'] = $user['uid'];
        $info['username'] = $user['username'];
        $info['email'] = $user['email'];
        $info['studentId'] = $user['jw_student_id'];
        $info['mobile'] = $user['mobile'];
        $info['wechat'] = $user['wechat'];
        $info['createTime'] = $user['create_time'];
        $info['lastLoginIp'] = '上次登陆IP：'.$user['last_login_ip'];
        return JsonData(200, $info, '获取用户信息成功！');
    }
    // 更新用户信息
    public function updateInfo(Request $request) {
        if (!$request->isLogin) {
            return JsonData(300, false, '请先登陆！');
        }
        $data = $this->request->param();
        // PHP中匹配汉字与其他语言不一样
        $usernameRe = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u';
        $mobileRe = '/^1[0-9]{10}$/';
        $wechatRe = '/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/';
        if ($data['username'] && !preg_match($usernameRe, $data['username'])) {
            return JsonData(400, false, '用户名格式错误！');
        }
        if ($data['mobile'] && !preg_match($mobileRe, $data['mobile'])) {
            return JsonData(400, false, '手机号格式错误！');
        }
        if ($data['wechat'] && !preg_match($wechatRe, $data['wechat'])) {
            return JsonData(400, false, '微信号格式错误！');
        }
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        $user['username'] = $data['username'];
        $user['email'] = $data['email'];
        $user['jw_student_id'] = $data['studentId'];
        $user['mobile'] = $data['mobile'];
        $user['wechat'] = $data['wechat'];
        $flag = $user->save();
        if ($flag) {
            Session::clear();
            Session::set('uid', $user->uid);
            Session::set('user_info',$user);
            return JsonData(200, true, '保存成功');
        } else {
            return JsonData(400, false, '保存失败');
        }
    }
    // 校验密码
    public function checkPwd(Request $request) {
        if (!$request->isLogin) {
            return JsonData(300, false, '请先登陆！');
        }
        $data = $this->request->param();
        $password = md5($data['password']);
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        if (!$user) {
            return JsonData(400, false, '系统运行错误');
        }
        if ($user['password'] == $password) {
            return JsonData(200, true, '密码正确');
        } else {
            return JsonData(400, false, '密码错误');
        }
    }
    // 更新密码
    public function updatePwd(Request $request) {
        if (!$request->isLogin) {
            return JsonData(300, false, '请先登陆！');
        }
        $re = '/^(?=.*\d)(?=.*[a-zA-Z]).{8,25}$/';
        $data = $this->request->param();
        if (!preg_match($re, $data['password'])) {
            return JsonData(400, false, '密码格式错误！');
        }
        $password = md5($data['password']);
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        $user['password'] = $password;
        $flag = $user->save();
        if ($flag) {
            Session::clear();
            return JsonData(200, true, '密码更改成功！');
        } else {
            return JsonData(400, false, '系统运行错误！');
        }
    }
    // 更新教务账号
    public function updateStuInfo(Request $request) {
        if (!$request->isLogin) {
            return JsonData(300, false, '请先登陆！');
        }
        $data = $this->request->param();
        if (!$data['studentId'] || !$data['studentPwd']) {
            return JsonData(400, false, '请填写正确！');
        }
        $uid = Session::get('uid');
        $user = UserModel::get($uid);
        $user['jw_student_id'] = $data['studentId'];
        $user['jw_student_pwd'] = $data['studentPwd'];
        $flag = $user->save();
        if ($flag) {
            return JsonData(200, true, '修改成功！');
        } else {
            return JsonData(400, false, '系统运行错误！');
        }
    }
}