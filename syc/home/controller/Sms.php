<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/17
 * Time: 10:14
 */

namespace app\home\controller;


use app\home\model\User;
use smsbao\SmsBao;
use think\Controller;

class Sms extends Controller
{
    public function index()
    {
        $sms = new SmsBao('sanyecao','dwhhbqn');
        //生成一个验证码
        $phone = $_POST['phone'];
        $code = $sms->getcode();
        $arr['phone'] = $phone;
        $arr['code'] = $code;
        //将验证码放入session中
        session('regcode',$arr);
        $content = "【三叶草】您的验证码为".$code."，请勿将验证码提供给他人。";
        $user = new User();
        $lists = $user->where("username",$phone)->where("state","<",3)->find();
        if($lists == null) {
            //发送短信
            $msg = $sms->sendSms($phone, $content);
            if ($msg['status'] == 0) {
                return '已发送！';
            } else {
                return '发送失败，请在此点击！';
            }
        }else{
            return '该用户已被注册！';
        }
    }

    public function index1()
    {
        $sms = new SmsBao('sanyecao','dwhhbqn');
        //生成一个验证码
        $phone = $_POST['phone'];
        $code = $sms->getcode();
        $arr['phone'] = $phone;
        $arr['code'] = $code;
        //将验证码放入session中
        session('regcode',$arr);
        $content = "【三叶草】您的验证码为".$code."，请勿将验证码提供给他人。";
        $user = new User();
        $lists = $user->where("username",$phone)->where("state","<",3)->find();
        if($lists == null) {
            return '该用户已被注册！';
        }else{
            //发送短信
            $msg = $sms->sendSms($phone,$content);
            if ($msg['status'] == 0) {
                return '已发送！';
            } else {
                return '发送失败，请在此点击！';
            }
        }
    }
}