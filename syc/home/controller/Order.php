<?php

namespace app\home\controller;
use think\Controller;
use think\Db;

class Order extends Controller
{
    public function index(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        $number = 0;
        $total = 0;
        $uid = session('homeuser')->id;
        $car = $_POST['car'];
        $lists = Db::table("syc_car")->where("uid",$uid)->where("id","in",$car)->order("id desc")->select();
        foreach ($lists as $k => $v) {
            $number += $v['number'];
            $total += $v['number']*$v['price'];
        }
        $address = Db::table("syc_address")->where("uid",$uid)->order("state")->select();
        $this->assign("address",$address);
        $this->assign("lists",$lists);
        $this->assign("number",$number);
        $this->assign("total",$total);
        return $this->fetch("index");
    }

    public function index2(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //用户id
        $uid = session('homeuser')->id;
        //购物车中该商品的id
        $id = $_POST['id'];
        //查出提交过来的商品信息
        $lists = Db::table("syc_car")->where("uid",$uid)->where("id","in",$id)->select();
        //收货地址id
        $aid = $_POST['address'];
        //查出收货地址信息
        $address = Db::table("syc_address")->where("id",$aid)->find();
        //随机生成一个订单号
        $orderid = rand(111,999).time().rand(11111,99999);
        //总金额
        $total = $_POST['total'];
        $time = time();
        if(!$lists){
            //给订单详情表中添加信息
            $buydetail = array();
            $buydetail['uid'] = $uid;
            $buydetail['orderid'] = $orderid;
            $buydetail['goodsid'] = $id[0];
            //查出提交过来的商品信息
            $good = Db::table("syc_goods")->where("id",$buydetail['goodsid'])->find();
            $buydetail['name'] = $good['goods'];
            $buydetail['picname'] = $good['picname1'];
            $buydetail['price'] = $good['price'];
            $buydetail['size'] = $_POST['size'];
            $buydetail['num'] = $_POST['number'];
            $buydetail['time'] = $time;
            $buydetail['state'] = 1;
            //执行添加 返回id
            $m = Db::table('syc_detail')->insert($buydetail);
            if($m>0){
                //查出商品信息
                $goods = Db::table("syc_goods")->where("id",$buydetail['goodsid'])->find();
                //增加销售量
                $num = $goods['num']+$buydetail['num'];
                Db::table('syc_goods')->where('id',$buydetail['goodsid'])->update(['num'=>$num]);
                //查出尺码id
                $size = Db::table("syc_size")->where('name',$buydetail['size'])->find();
                $sid = $size['id'];
                //查出库存
                $goods_size = Db::table("syc_goods_size")->where("gid",$buydetail['goodsid'])->where("sid",$sid)->find();
                //减少库存
                $store = $goods_size['store']-$buydetail['num'];
                Db::table('syc_goods_size')->where('id',$goods_size['id'])->update(['store'=>$store]);
            }
        }else{
            //遍历商品信息
            foreach ($lists as $k => $v) {
                //给订单详情表中添加信息
                $detail = array();
                $detail['uid'] = $uid;
                $detail['orderid'] = $orderid;
                $detail['goodsid'] = $v['gid'];
                $detail['name'] = $v['goods'];
                $detail['picname'] = $v['picname1'];
                $detail['price'] = $v['price'];
                $detail['size'] = $v['size'];
                $detail['num'] = $v['number'];
                $detail['time'] = $time;
                $detail['state'] = 1;
                //执行添加 返回id
                $m = Db::table('syc_detail')->insert($detail);
                if($m>0){
                    //查出商品信息
                    $goods = Db::table("syc_goods")->where("id",$v['gid'])->find();
                    //增加销售量
                    $num = $goods['num']+$v['number'];
                    Db::table('syc_goods')->where('id',$v['gid'])->update(['num'=>$num]);
                    //查出尺码id
                    $size = Db::table("syc_size")->where('name',$v['size'])->find();
                    $sid = $size['id'];
                    //查出库存
                    $goods_size = Db::table("syc_goods_size")->where("gid",$v['gid'])->where("sid",$sid)->find();
                    //减少库存
                    $store = $goods_size['store']-$v['number'];
                    Db::table('syc_goods_size')->where('id',$goods_size['id'])->update(['store'=>$store]);
                }
            }
        }
        //给订单表中添加信息
        $data = array();
        $data['orderid'] = $orderid;
        $data['uid'] = $uid;
        $data['linkman'] = $address['name'];
        $data['address'] = $address['address'];
        $data['code'] = $address['code'];
        $data['phone'] = $address['phone'];
        $data['addtime'] = $time;
        $data['total'] = $total;
        $data['status'] = 1;
        //执行添加 返回id
        $m = Db::table('syc_orders')->insert($data);
        if($m>0){
            //删除提交过的商品
            Db::table('syc_car')->where("id","in",$id)->delete();
            $this->assign("orderid",$orderid);
            return $this->fetch("index2");
        }
    }

    public function index3()
    {
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        $number = $_POST['num'];
        $size = $_POST['size'];
        $gid = $_POST['gid'];
        $goods = Db::table("syc_goods")->where("id",$gid)->find();
        $total = $number*$goods['price'];
        $uid = session('homeuser')->id;
        $address = Db::table("syc_address")->where("uid",$uid)->order("state")->select();
        $lists = null;
        $this->assign("lists",$lists);
        $this->assign("address",$address);
        $this->assign("goods",$goods);
        $this->assign("number",$number);
        $this->assign("size",$size);
        $this->assign("total",$total);
        return $this->fetch("index");
    }

    public function edit()
    {
        $id = $_POST['id'];
        $list = Db::table("syc_address")->where("id",$id)->find();
        if($list){
            $upid = isset($_GET['upid'])?$_GET['upid']:0;
            $list1 = Db::query('select * from syc_district where upid=?',[$upid]);
            $data['list']=$list1;

            $data['id'] = $list['id'];
            $data['name'] = $list['name'];
            $data['address'] = explode("|",$list['address'])[1];
            $data['code'] = $list['code'];
            $data['phone'] = $list['phone'];
            echo json_encode($data);
        }
    }

    public function ok()
    {
        $id = $_POST['id'];
        $data['name'] = input('name');
        $data['address'] = input('address');
        $data['phone'] = input('phone');
        $data['code'] = input('code');
        $list = Db::table("syc_address")->where('id',$id)->update($data);
        if($list>0)
        {
            return true;
        }
    }

    public function add()
    {
        $upid = isset($_GET['upid'])?$_GET['upid']:0;
        $list1 = Db::query('select * from syc_district where upid=?',[$upid]);
        $data['list']=$list1;
        echo json_encode($data);
    }

    public function insert()
    {
        $data['uid'] = session("homeuser")->id;
        $data['name'] = input('name');
        $data['address'] = input('address');
        $data['phone'] = input('phone');
        $data['code'] = input('code');
        $list = Db::table("syc_address")->insertGetId($data);
        if($list>0){
            return $list;
        }
    }
}