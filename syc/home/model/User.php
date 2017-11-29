<?php


namespace app\home\model;


use think\Model;

class User extends Model
{
    protected $insert = ['addtime','pass'];

    public function setAddtimeAttr()
    {
        return time();
    }

    public function setPassAttr($value)
    {
        return md5($value);
    }
}