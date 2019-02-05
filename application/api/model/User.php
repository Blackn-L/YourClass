<?php
namespace app\api\model;
use think\Model;

class User extends Model
{
    protected $pk= 'uid';
    // 自动写入创建和更新时间
    protected $autoWriteTimestamp = 'datetime';
    // 关闭自动写入更新时间
    protected $updateTime = false;
}