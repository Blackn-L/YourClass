<?php
namespace app\api\controller;
use think\Controller;
use app\api\model\User as UserModel;
class Register extends Controller
{
    public function index()
    {
        $data = $this->request->param();
        $email = $data['email'];
//        $isEmail = UserModel::where('email', $email)->find();
//        if ($isEmail != Null) {
//            return ('当前邮箱已注册');
//        };
//        $username = $data['username'];
//        $password = $data['passowrd'];
        return($email);
    }
}
