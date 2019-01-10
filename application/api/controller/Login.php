<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Session;
use app\api\model\User as UserModel;
class Login extends Controller
{
    public function index() {
        $data = $this->request->param();
        // 判断验证码是否正确
        if (!captcha_check($data['code'])) {
            return JsonData(400,null,'验证码错误');
        }
        $validate = new \app\api\validate\Login;
        if (!$validate->check($data)) {
            return JsonData(400,null,$validate->getError());
        }
        $email = $data['email'];
        // 后期考虑邮箱和用户名都可以登录
        $user = UserModel::where('email', $email)->find();
        if (!$user) {
            return JsonData(400,null,'当前用户尚未注册');
        }
        $password = md5(trim($data['password']));
        if ($user->password != $password) {
            return JsonData(400,null,'密码错误');
        }
        // 删除原本session
        Session::clear();
        // 获取Session会话 ID
        Session::set('uid', $user->uid);
        Session::set('user_info',$user);

        return JsonData(200,null,'登陆成功');
    }
}