<?php

namespace app\admin\model;
use think\Model;

class Goods extends Model
{
    protected $insert = ['addtime'];

    public function setAddtimeAttr()
    {
        return time();
    }
}