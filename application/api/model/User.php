<?php
namespace app\api\model;
use think\Model;

class User extends Model
{
    protected $pk= 'uid';
    // 自动写入创建和更新时间
    protected $autoWriteTimestamp = 'datetime';
    // 自定义自动更新时间的字段名
    protected $updateTime = 'last_login_time';
}