<?php
namespace app\home\controller;

use app\admin\model\Goods;
use app\home\model\Type;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //查出商品信息
        $new = Db::table("syc_goods")->where("state","2")->order("addtime desc")->limit(8)->select();
        $this->assign("new",$new);

        $hot = Db::table("syc_goods")->where("state","2")->order("num desc")->limit(6)->select();
        $this->assign("hot",$hot);

        $shoe = Db::table("syc_goods")->where("state","2")->where("typeid",58)->limit(8)->select();
        $this->assign("shoe",$shoe);

        $clo = Db::table("syc_goods")->where("state","2")->where("typeid",66)->limit(8)->select();
        $this->assign("clo",$clo);

        $watch = Db::table("syc_goods")->where("state","2")->where("typeid",77)->limit(4)->select();
        $this->assign("watch",$watch);

        $rank = Db::table("syc_goods")->order("num desc")->limit(6)->select();
        $this->assign("rank",$rank);
        //加载模板
        return $this->fetch("index");
    }

    public function find(){
        //查出对应的商品信息 并返回json格式数据
        $id = $_POST['id'];
        $goods = new Goods();
        $list = $goods->where("state","2")->where("typeid",$id)->limit(4)->select();
        echo json_encode($list);
    }

    public function carnum()
    {
        //查出购物车的数量
        if(session("homeuser")){
            $uid = session('homeuser')->id;
            $number = Db::table("syc_car")->where('uid',$uid)->select();
            $num=0;
            foreach ($number as $v){
                $num += $v['number'];
            }
        }else{
            $num = 0;
        }
        return $num;
    }
}
