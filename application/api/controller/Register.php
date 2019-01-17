<?php
namespace app\api\controller;
use think\Controller;
use app\api\model\User as UserModel;
use think\facade\Session;

class Register extends Controller
{
    public function index()
    {
        $data = $this->request->param();
        $validate = new \app\api\validate\Register;
        if (!$validate->check($data)) {
            return JsonData(400,null,$validate->getError());
        }
        $trueEmailCode = Session::get('emailCode');
        if ($data['emailCode'] != $trueEmailCode) {
            return JsonData(400,null,'邮箱验证码错误！');
        }
        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $isEmail = UserModel::where('email', $email)->find();
        if ($isEmail != null) {
            return JsonData(400,null,'当前邮箱已注册！');
        };
        $isUsername = UserModel::where('username', $username)->find();
        if ($isUsername != null) {
            return JsonData(400, null, "当前用户名已存在！");
        }
        $newUser = new UserModel;
        $newUser['email'] = $email;
        $newUser['username'] = $username;
        $newUser['password'] = md5($password);
        $status = $newUser->allowField(true)->save($newUser);
        if ($status) {
            return JsonData(200,null,"注册成功！");
        };
        return JsonData(400,null,"系统运行错误!");
    }
    public function sendEmail() {
        $title = '测试邮件';
        $toEmail = '1481940986@qq.com';
        $code = mt_rand(100000,999999);
        $name = '测试用户';
        $body = '您的验证码是：'.$code;
        $result=send_mail($toEmail, $name, $title, $body);
        if($result){
            //记录邮件验证码
            session('emailCode',$code);
            return JsonData(200,null,'发送成功！');
        }else{
            return JsonData(400,null,'发送失败！');
        }
    }
}