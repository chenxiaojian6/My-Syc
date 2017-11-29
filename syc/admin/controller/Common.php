<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;


use think\Controller;

class Common extends Controller
{
    //在controller类中构造方法执行后会自动调用
    public function _initialize()
    {
        //是否登陆验证
        $adminuser = session('adminuser');
        if(empty($adminuser)){
            $this->redirect('login/index');
            exit();
        }

        //特殊用户（超级管理员）
        if(session('adminuser')['username']=='admin'){
            return;
        }
        //输出用户的权限列表
        $nodelist = session('nodelist');
        //获取当前控制器名和方法名
        $request = request();
        $cname = strtoLower($request->controller());
        $aname = strtoLower($request->action());

        //判断当前用户是否拥有此权限
        if(empty($nodelist[$cname]) || !in_array($aname,$nodelist[$cname])){
            $this->error('抱歉，您没有此操作权限！');
            exit();
        }
    }
}