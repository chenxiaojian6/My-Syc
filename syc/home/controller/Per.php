<?php

namespace app\home\controller;
use think\Controller;
use think\Db;

class Per extends Controller
{
    public function index(){
        if(session("homeuser")==null){
            return $this->redirect("/home/index/index");
            exit();
        }
        $uid = session("homeuser")->id;
        $num = Db::table("syc_orders")->where("uid",$uid)->where("status",3)->count();
        $this->assign("num",$num);
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        return $this->fetch("index");
    }
}