<?php

namespace app\home\controller;
use think\Controller;
use think\Db;
use app\home\model\User;

class Link extends Controller
{
    public function index(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        return $this->fetch("index");
    }

    public function username(){
        $name = input('username');
        $list = User::where(['username'=>$name,'state'=>1])->find();
        if($list){
            return true;
        }else{
            return false;
        }
    }

    public function index1(){
        //获取数据
        $name = input('username');
        $code = input('code');
        $user = User::where(['username'=>$name,'state'=>1])->find();
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //验证码是否正确
        if(captcha_check($code)){
            //判断用户名是否正确
            if($user){
                $this->assign('name',$name);
                return $this->fetch('index1');
                exit();
            }else{
                $this->assign('name',$name);
                $this->assign('errorinfo','账号输入有误');
                return $this->fetch('index');
                exit();
            }
        }else{
            $this->assign('name',$name);
            $this->assign('errorinfo','验证码错误');
            return $this->fetch('index');
            exit();
        }
    }

    public function index2(){
        $name = input("name");
        $code = input("code");
        $ses = session("regcode");
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        if ($code == null) {
            $this->assign('name',$name);
            $this->assign('errorinfo', '请输入验证码!');
            return $this->fetch('index1');
            exit;
        }
        if ($ses['code'] == $code) {
                $this->assign('name',$name);
                return $this->fetch('index2');
            exit();
        } else {
            $this->assign('name',$name);
            $this->assign('errorinfo', '手机验证码输入错误!');
            return $this->fetch('index1');
            exit();
        }
    }

    public function index3(){
        $pass1 = input('pass1');
        $pass2 = input('pass2');
        $username = input('username');
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        if ($pass1 == null) {
            $this->assign('errorinfo','密码不能为空!');
            $this->assign('name',$username);
            return $this->fetch('index2');
            exit();
        }
        if ($pass2 == null) {
            $this->assign('errorinfo','确认密码不能为空!');
            $this->assign('name',$username);
            return $this->fetch('index2');
            exit();
        }
        if(!preg_match("/^\w{6,14}$/","$pass1")){
            $this->assign('errorinfo','密码格式错误!');
            $this->assign('name',$username);
            return $this->fetch('index2');
            exit();
        }
        if($pass1 != $pass2){
            $this->assign('errorinfo','密码与确认密码不一致!');
            $this->assign('name',$username);
            return $this->fetch('index2');
            exit();
        }
        $m = Db::table('syc_user')->where('username',$username)->update(['pass' => md5($pass1)]);
        if($m>0){
            echo "<script>alert('密码修改成功,请登录');window.location.href='/home/login/index';</script>";
        }
    }
}