<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Session;
use app\api\model\User as UserModel;
class Login extends Controller
{
    public function index() {
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

        return JsonData(200,true,'登陆成功');
    }
}