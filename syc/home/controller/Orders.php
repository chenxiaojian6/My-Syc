<?php
namespace app\home\controller;

use think\Controller;
use think\Db;
use app\home\model\User;

class Orders extends Controller
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
        //获取用户id
        $uid = session('homeuser')->id;
        //查出该用户的所有订单
        $orders = Db::table("syc_orders")->where("uid",$uid)->order("id desc")->select();
        $this->assign("orders",$orders);
        //加载模板
        return $this->fetch("index");
    }

    public function index2(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //获取订单号
        $id = input("id");
        //查出该订单
        $orders = Db::table("syc_orders")->where("id",$id)->find();
        //查出订单详情
        $orders_detail = Db::query("select d.* from syc_detail d,syc_orders o where d.orderid = o.orderid and o.id = ".$id);
        $this->assign("ordersstate",$orders['status']);
        $this->assign("orders_detail",$orders_detail);
        return $this->fetch("index2");
    }
    public function esc()
    {
        $id = $_POST['id'];
        $list = Db::table("syc_orders")->where("id",$id)->find();
        $orderid = $list['orderid'];
        $data = Db::table("syc_detail")->where('orderid',$orderid)->select();
        foreach ($data as $v)
        {
            $numb = Db::table("syc_size")->where('name',$v['size'])->find();
            $sid = $numb['id'];

            $store = Db::table("syc_goods_size")->where('sid',$sid)->where("gid",$v['goodsid'])->find();
            $num = $store['store']+$v['num'];
            $storenum = Db::table("syc_goods_size")->where('sid',$sid)->where("gid",$v['goodsid'])->update(['store'=>$num]);
        }
        $m = Db::table("syc_orders")->where('id',$id)->update(['status'=>4]);
        if($m>0)
        {
            return true;
        }
    }
    public function rece()
    {
        $id = $_POST['id'];
        $m = Db::table("syc_orders")->where('id',$id)->update(['status'=>3]);
        if($m>0)
        {
            return true;
        }
    }
}