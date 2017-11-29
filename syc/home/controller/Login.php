<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/9
 * Time: 17:21
 */

namespace app\home\controller;


use app\home\model\User;
use think\Controller;
use think\Db;
use Geetest\Geetest;

class Login extends Controller
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

    public function username(){
        $name = input('username');
        $list = User::where(['username'=>$name,'state'=>1])->find();
        if($list){
            return true;
        }else{
            return false;
        }
    }

    public function pass(){
        $user = new User;
        $name = input('name');
        $pass = md5(input('pass'));
        $list = $user->where('username',$name)->where('state',1)->find();
        if($list){
            if ($list->pass == $pass) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * geetest生成验证码
     */
    public function show(){
        $geetest_id="5282386725b8a5438bb5c1e48482cc0c";
        $geetest_key="f3045fae01d1d6e64c730afda6d5380f";
        $geetest=new Geetest($geetest_id,$geetest_key);
        $user_id = "test";
        $status = $geetest->pre_process($user_id);
        $_SESSION['geetest']=array(
            'gtserver'=>$status,
            'user_id'=>$user_id
            );
        echo $geetest->get_response_str();
    }

    public function dologin(){
        //获取数据
        $name = input('username');
        $pass = md5(input('pass'));
        // $code = input('code');
        $data['geetest_challenge'] = $_POST['geetest_challenge'];
        $data['geetest_validate'] = $_POST['geetest_validate'];
        $data['geetest_seccode'] = $_POST['geetest_seccode'];
        $user = User::where(['username'=>$name,'state'=>1])->find();
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //验证码是否正确
        if($this->geetest_chcek_verify($data)){
            //判断用户名是否正确
            if($user){
                //判断密码是否正确
                if($user->pass == $pass){
                    session('homeuser',$user);
                    $this->redirect('http://www.xiaojian716.club/');
                    exit();
                }else{
                    $this->assign('errorinfo','密码输入有误');
                    return $this->fetch('index');
                    exit();
                }
            }else{
                $this->assign('errorinfo','账号输入有误');
                return $this->fetch('index');
                exit();
            }
        }else{
            $this->assign('errorinfo','验证码错误');
            return $this->fetch('index');
            exit();
        }
    }

    /**
     * geetest检测验证码
     */
    function geetest_chcek_verify($data){
        $geetest_id="5282386725b8a5438bb5c1e48482cc0c";
        $geetest_key="f3045fae01d1d6e64c730afda6d5380f";
        $geetest=new Geetest($geetest_id,$geetest_key);
            $result=$geetest->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode']);
            if ($result) {
                return true;
            } else{
                return false;
            }
    }

    public function logout()
    {
        session('homeuser',null);
        $this->redirect('http://www.xiaojian716.club/');
        exit();
    }

    //QQ登录
    public function qq(){
        //随机字符串
        $state = md5(rand(1,9999));
        $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=101427890&redirect_uri=http://www.xiaojian716.club/home/login/code&state=".$state;
        session("state",$state);
        header('location:'.$url);
    }

    //QQ回调地址 获取access_token 和 openid
    public function code(){
        //获取co的和state
        $code = $_GET['code'];
        $state = $_GET['state'];
        $redirect_uri = urlencode("http://www.xiaojian716.club/home/login/code");
        //判断state
        if($state != session("state")){
            echo "error : state !";
        }else{
            //获取access_token
            $url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=101427890&client_secret=47eb6362d77f3c20c2b4ed46b3019e57&code=".$code."&redirect_uri=".$redirect_uri;
            $res = $this->url($url);
            parse_str($res,$params);
            $access_token = $params['access_token'];
            //获取openid
            $url_openid = "https://graph.qq.com/oauth2.0/me?access_token=".$access_token;
            $res_openid = $this->url($url_openid);
            //截取字符串
            if(strpos($res_openid, "callback") !== false){
                $lpos = strpos($res_openid, "(");
                $rpos = strrpos($res_openid, ")");
                $res_openid = substr($res_openid, $lpos + 1, $rpos - $lpos -1);
            }
            $user = json_decode($res_openid,true);
            $openid = $user['openid'];
            $user_url = "https://graph.qq.com/user/get_user_info?access_token=".$access_token."&oauth_consumer_key=101427890&openid=".$openid;
            $userinfo = $this->url($user_url);
            $use = json_decode($userinfo,true);
            $pic = $use['figureurl_qq_2'];
            $qqname = $use['nickname'];
            if($use['gender'] == "男"){
                $sex = 1;
            }else{
                $sex = 2;
            }
            $user = new User;
            $userid = $user->where("qq",$openid)->find();
            if($userid){
                //若有则直接登录
                session('homeuser',$userid);
                $this->redirect('http://www.xiaojian716.club/');
                exit();
            }else{
                //否则将数据添加到数据库
                $data['qq'] = $openid;
                $data['qqname'] = $qqname;
                $data['headpic'] = $pic;
                $data['sex'] = $sex;
                $data['username'] = md5(rand(1,99999));
                $result = $user->save($data);
                if($result>0){
                    $user_id = $user->where('qq',$openid)->find();
                    session('homeuser',$user_id);
                    $this->redirect('http://www.xiaojian716.club/');
                    exit();
                }
            }
        }
    }

    //微博登录
    public function wb(){
        //跳转页面
        $url = "https://api.weibo.com/oauth2/authorize?client_id=1020693463&response_type=code&redirect_uri=http://www.xiaojian716.club/home/login/wb_two";
        header('location:'.$url);
    }

    //微博回调地址
    public function wb_two(){
        //判断是否有接收的code
        if(empty($_GET['code'])){
            $this->redirect('/home/login/index');
        }else{
            //获取access_token和uid
            $code = $_GET['code'];
            $url = "https://api.weibo.com/oauth2/access_token?client_id=1020693463&client_secret=ec15f3ce39bb7fcab824611dbe82ae5c&grant_type=authorization_code&redirect_uri=http://www.xiaojian716.club/home/login/wb_two&code=".$code;
            $res = $this->url($url,"post");
            $arr = json_decode($res,true);
            //获取用户信息
            $user = "https://api.weibo.com/2/users/show.json?access_token=".$arr['access_token']."&uid=".$arr['uid'];
            $users = $this->url($user);
            $user_arr = json_decode($users,true);
            $pic = $user_arr['avatar_large'];
            if($user_arr['gender'] == "m"){
                $sex = 1;
            }else{
                $sex = 2;
            }
            $user = new User;
            //查出是否有该用户
            $userid = $user->where('wb',$user_arr['idstr'])->find();
            if($userid){
                //若有则直接登录
                session('homeuser',$userid);
                $this->redirect('http://www.xiaojian716.club/');
                exit();
            }else{
                //否则将数据添加到数据库
                $data['wb'] = $user_arr['idstr'];
                $data['wbname'] = $user_arr['screen_name'];
                $data['headpic'] = $pic;
                $data['sex'] = $sex;
                $data['username'] = md5(rand(1,99999));
                $result = $user->save($data);
                if($result>0){
                    $user_id = $user->where('wb',$user_arr['idstr'])->find();
                    session('homeuser',$user_id);
                    $this->redirect('http://www.xiaojian716.club/');
                    exit();
                }
            }
        }
    }

    //封装
    public function url($url,$type='get',$res='json',$arr=''){
        //1,初始化
        $ch = curl_init();
        //2,设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        if($type=='post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        //3,调用接口
        $exec = curl_exec($ch);
        //4,关闭
        curl_close($ch);
        if(empty($exec)){
           return curl_errno($ch);
        }else{
           return $exec;
        }
    }
}