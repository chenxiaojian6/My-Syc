<?php

namespace app\home\controller;
use think\Controller;
use think\Db;
use app\home\model\User;

class Pass extends Controller
{
    public function index(){
        if(session("homeuser")==null){
            return $this->redirect("/home/index/index");
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

    public function edit(){
        return $this->fetch('edit');
    }

    public function oldpass(){
        $pass = session("homeuser")->pass;
        $oldpass = md5(input('pass'));
        if ($pass == $oldpass){
            return true;
        }else{
            return false;
        }
    }

    public function update(){
        if(session("homeuser")==null){
            return $this->redirect("/home/index/index");
            exit();
        }
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        $name = session('homeuser')->username;
        $oldpass = input('oldpass');
        $pass1 = input('pass1');
        $pass2 = input('pass2');
        $code = input('code');
        $pass = session("homeuser")->pass;
        if(!captcha_check($code))
        {
            $this->assign('errorinfo',"验证码输入错误!");
            return $this->fetch('index');
            exit;
        }else{
            if(!preg_match("/^\w{6,14}$/",$oldpass)){
                $this->assign('errorinfo','旧密码格式错误!');
                return $this->fetch('index');
                exit();
            }
            if(!preg_match("/^\w{6,14}$/",$pass1)){
                $this->assign('errorinfo','新密码格式错误!');
                return $this->fetch('index');
                exit();
            }
            if(!preg_match("/^\w{6,14}$/",$pass2)){
                $this->assign('errorinfo','确认新密码格式错误!');
                return $this->fetch('index');
                exit();
            }
            if(md5($oldpass)!=$pass){
                $this->assign('errorinfo',"原密码输入错误!");
                return $this->fetch('index');
                exit;
            }else{
                if($pass1!=$pass2){
                    $this->assign('errorinfo',"新密码与确认密码不一致!");
                    return $this->fetch('index');
                    exit;
                }else{
                    if($oldpass==$pass1){
                        $this->assign('errorinfo',"新密码不能与原密码一致!");
                        return $this->fetch('index');
                        exit;
                    }else{
                        $user = new User;
                        $m = $user->save(['pass'=>$pass1],['username'=>$name]);
                        if($m>0){
                            session("homeuser",null);
                            echo "<script>alert('密码修改成功,请重新登录');window.location.href='http://www.xiaojian716.club/login.html';</script>";
                        }else{

                        }
                    }
                }
            }
        }
    }
}