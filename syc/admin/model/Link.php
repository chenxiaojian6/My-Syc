<?php

namespace app\admin\model;


use think\Model;

class Link extends Model
{
    protected $insert = ['addtime'];

    public function setAddtimeAttr()
    {
        return time();
    }
}