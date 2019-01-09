<?php
namespace app\api\controller;
use think\Controller;
use app\api\model\User as UserModel;
class Register extends Controller
{
    public function index()
    {
        $data = $this->request->param();
        $validate = new \app\api\validate\Register;
        if (!$validate->check($data)) {
            return JsonData(400,null,$validate->getError());
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
}
