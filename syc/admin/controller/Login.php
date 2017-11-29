<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;

use app\admin\model\User;
use think\Controller;
use think\Db;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch('login');
    }

    public function dologin(){
        header("Content-Type:text/html;charset=utf-8");
        //获取数据
        $name = input('name');
        $pass = md5(input('pass'));
        $code = input('code');
        $list = User::where(['username'=>$name,'state'=>3])->find();
        //验证码是否正确
        if(captcha_check($code)){
            //判断用户名是否正确
            if($list){
                //判断密码是否正确
                if($list->pass == $pass){
                    session('adminuser',$list);
                    //获取当前用户的权限列表
                    $lists  = Db::query("select n.mname,n.aname from syc_users_role ur,syc_role_node rn,syc_node n where ur.rid=rn.rid and rn.nid=n.id and ur.uid = ".$list['id']);

                    $list = array();
                    foreach($lists as $v){
                        $list[] = implode(',',$v);
                    }
                    //去除重复值
                    $list = array_unique($list);
                    $lists = array();
                    foreach($list as $k=>$v){
                        $arr = explode(',',$v);
                        $lists[$k]['mname']=$arr[0];
                        $lists[$k]['aname']=$arr[1];
                    }

                    //绑定权限
                    $nodelist['index'][] = 'index';//
                    foreach($lists as $v){
                        $nodelist[$v['mname']][] = $v['aname'];

                        //若有add权限，则让他有insert权限
                        if($v['aname'] == 'add'){
                            $nodelist[$v['mname']][] = 'insert';
                        }

                        //若有edit权限，则让他有update权限
                        if($v['aname'] == 'edit'){
                            $nodelist[$v['mname']][] = 'update';
                        }
                    }
                    session('nodelist',$nodelist);//设置权限session
                    $this->redirect('admin/index/index');
                    exit();
                }else{
                    $this->assign('errorinfo','密码输入有误');
                    return $this->fetch('login');
                    exit();
                }
            }else{
                $this->assign('errorinfo','账号输入有误');
                return $this->fetch('login');
                exit();
            }
        }else{
            $this->assign('errorinfo','验证码错误');
            return $this->fetch('login');
            exit();
        }
    }
    public function logout()
    {
        session('adminuser',null);
        session('nodelist',null);
        $this->redirect('admin/login/index');
        exit();
    }
}