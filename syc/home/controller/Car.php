<?php

namespace app\home\controller;
use think\Controller;
use think\Db;

class Car extends Controller
{
    public function index(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //判断用户是否登录
        if(session("homeuser")){
            // 获取用户id
            $uid = session('homeuser')->id;
            //查出该用户的所有购物车中的商品
            $car = Db::table("syc_car")->where('uid',$uid)->order('id desc')->select();
            //判断该用户购物车是否有东西
            if($car){
                $num = 0;
                //将所有的数量累加
                foreach ($car as $v) {
                    $num += $v['number'];
                }
                $this->assign("carlist", $car);
            }else{
                $num = 0;
                $car = null;
                $this->assign("carlist",$car);
            }
        }else{
            $car = null;
            $this->assign("carlist",$car);
            $num = 0;
        }
        $this->assign("num",$num);

        return $this->fetch("index");
    }

    //购物车数量加
    public function add(){
        //获取用户id
        $uid = session('homeuser')->id;
        //接收购物车该商品的id
        $id = $_POST['id'];
        //在购物车表中查出改信息
        $lis = Db::table("syc_car")->where('uid',$uid)->where("id",$id)->find();
        //查出该用户该商品的数量
        $a = (int)$lis['number'];
        //查出该商品的尺码id
        $numb = Db::table("syc_size")->where('name',$lis['size'])->find();
        $sid = $numb['id'];
        //查出该商品该尺寸的库存
        $list = Db::table("syc_goods_size")->where('gid',$lis['gid'])->where("sid",$sid)->find();
        $store = (int)$list['store'];
        //判断当前数量和库存的数量
        if($a>=$store){
            //如果当前数量大于或等于库存 则等于库存
            $number = $store;
            $addnum = 0;
        }else{
            //否则 数量加一
            $number = $a+1;
            $addnum = 1;
        }
        //修改购物车中的的数量
        $m = Db::table("syc_car")->where("id",$id)->update(["number"=>$number]);
        //再查出当前商品的信息
        $num = Db::table("syc_car")->where('uid',$uid)->where("id",$id)->find();
        //放到数组中
        $new['id'] = (int)$id;
        $new['number'] = (int)$num['number'];
        $new['addnum'] = (int)$addnum;
        $new['total'] = $num['price']*$num['number'];
        //转成json数据 返回到前台
        echo json_encode($new);
    }

    public function subtract(){
        //获取用户id
        $uid = session('homeuser')->id;
        //接收购物车该商品的id
        $id = $_POST['id'];
        //查出该商品的数量
        $goods = Db::table("syc_car")->where('uid',$uid)->where('id',$id)->find();
        $num = (int)$goods['number'];
        //判断数量
        if($num<2){
            //小于2则直接等于一
            $number = 1;
            $addnum = 0;
        }else{
            //否则 数量减一
            $number = $num-1;
            $addnum = 1;
        }
        //修改购物车中的的数量
        $m = Db::table("syc_car")->where("id",$id)->update(["number"=>$number]);
        //再查出当前商品的信息
        $num = Db::table("syc_car")->where("id",$id)->find();
        //放到数组中
        $new['id'] = (int)$id;
        $new['number'] = (int)$num['number'];
        $new['addnum'] = (int)$addnum;
        $new['total'] = $num['price']*$num['number'];
        //转成json数据 返回到前台
        echo json_encode($new);
    }

    public function del()
    {
        //接收购物车该商品的id
        $id = $_POST['id'];
        //查出该商品的信息
        $num = Db::table("syc_car")->where("id",$id)->find();
        //将改商品删除
        $m = Db::table("syc_car")->where("id",$id)->delete();
        if($m>0) {
            //将删除商品的数量返回到前台
            $n = $num['number'];
            $delnum = (int)$n;
            return $delnum;
        }
    }

    public function clear(){
        //获取用户id
        $uid = session('homeuser')->id;
        //删除该用户购物车中的数量
        $m = Db::table("syc_car")->where("uid",$uid)->delete();
        if($m>0){
            return 1;
        }else{
            return 0;
        }
    }
}