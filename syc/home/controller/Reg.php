<?php

namespace app\home\controller;
use think\Controller;
use think\Db;
use app\home\model\User;

class Reg extends Controller
{
    public function index(){
        if(session("homeuser")!=null){
            return $this->redirect("http://www.xiaojian716.club/");
            exit();
        }
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        return $this->fetch("index");
    }

    public function phone(){
        $phone = $_POST['phone'];
        $user = new User();
        $list = $user->where("username",$phone)->where("state","<",3)->find();
        if($list == null){
            return true;
        }else{
            return false;
        }
    }

    public function reg(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);

        $phone = input("phone");
        $pass1 = input("pass1");
        $pass2 = input("pass2");
        $code = input("code");
        $user = new User();
        $list = $user->where("username",$phone)->where("state","<",3)->find();
        if($list != null){
            $this->assign('errorinfo','该手机号已被注册!');
            return $this->fetch('index');
            exit();
        }
        if(!preg_match("/^1[34578]\d{9}$/","$phone")){
            $this->assign('errorinfo','手机号码格式错误!');
            return $this->fetch('index');
            exit();
        }
        if(!preg_match("/^\w{6,14}$/","$pass1")){
            $this->assign('errorinfo','密码格式错误!');
            return $this->fetch('index');
            exit();
        }
        if(!preg_match("/^\w{6,14}$/","$pass2")){
            $this->assign('errorinfo','确认密码格式错误!');
            return $this->fetch('index');
            exit();
        }
        if($pass1 != $pass2){
            $this->assign('errorinfo','密码与确认密码不一致!');
            return $this->fetch('index');
            exit();
        }
        $ses = session("regcode");
        if ($ses['phone'] == $phone && $ses['code'] == $code) {
            $user->username = $phone;
            $user->phone = $phone;
            $user->pass = $pass1;
            $user->state = 1;
            $a = $user->save();
            if ($a > 0) {
                $list = $user->where("username", $phone)->find();
                session("homeuser", $list);
                $this->redirect("http://www.xiaojian716.club/");
            }
        } else {
            $this->assign('errorinfo', '手机验证码输入错误!');
            return $this->fetch('index');
            exit();
        }
    }
}