<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;



class Index extends Common
{
    public function index()
    {
        return $this->fetch('index');
    }
}