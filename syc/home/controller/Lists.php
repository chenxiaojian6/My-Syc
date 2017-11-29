<?php

namespace app\home\controller;

use app\home\model\Goods;
use app\home\model\Type;
use think\Controller;
use think\Db;

class Lists extends Controller
{
    public function app(){
        $lists = Db::table("syc_goods")->select();
        return json_encode($lists);
    }
    public function detail(){
        $id = $_GET['id'];
        $list = Db::table("syc_goods")->where("id",$id)->find();
        return json_encode($list);
    }
    public function index(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //接收id
        $id = input('id');
        $type = new Type();
        if($id) {
            //判断该类别是否为子类
            $a = $type->where("pid", $id)->select();
            if (empty($a)) {
                //若该类别为子类 查出该类别下goods表中的商品
                $lists = Db::table("syc_goods")->where("state",2)->where("typeid", $id)->select();
                $sell = Db::table("syc_goods")->where("state",2)->where("typeid", $id)->order("num desc")->limit(7)->select();
                //查出type表中该类别的信息
                $a = $type->where("id", $id)->find();
                //查出该类别的父类别
                $pa = $type->where("id",$a->pid)->find();
                if($pa){
                    //拼接类别信息
                    $name = $pa->name . " > " . $a->name;
                    $typename = $pa->name;
                }else{
                    //拼接类别信息
                    $name = $a->name;
                    $typename = "";
                }
                //查出该类别的同级类别
                $types = $type->where("pid", $a->pid)->select();
            } else {
                //若该类为根类别 把该类别的子类别拼接成一个数组
                foreach ($a as $v) {
                    $b[] = $v['id'];
                }
                //查出该类别的子类别下的所有商品
                $lists = Db::table("syc_goods")->where("state",2)->where("typeid", "in", $b)->select();
                $sell = Db::table("syc_goods")->where("state",2)->where("typeid", "in", $b)->order("num desc")->limit(7)->select();
                //查出该根类别下的所有子类别
                $types = $type->where("pid",$id)->select();
                //查出该类别的信息
                $a = $type->where("id", $id)->find();
                //拼接类别信息
                $name = $a->name;
                $typename = $a->name;
            }
            $this->assign('id',$id);
        }else{
            //创建请求对象
            $request = request();
            //获取表单数据并封装搜索条件
            $where = array();//封装搜索条件
            if($request->param('goods')){
                $where['goods'] = ['like',"%".$request->param('goods')."%"];
            }
            //查出状态为2的商品
            $lists = Db::table('syc_goods')->where("state",2)->where($where)->select();
            //查出所有根类别
            $types = $type->where("pid",0)->select();
            $name = "";
            $typename = "三叶草";
            $sell = Db::table("syc_goods")->where("state",2)->order("num desc")->limit(6)->select();
        }
        $this->assign("lists", $lists);
        $this->assign("name", $name);
        $this->assign("typename", $typename);
        $this->assign("types", $types);
        $this->assign("sell", $sell);
        return $this->fetch("index");
    }

    public function sort()
    {
        //ajax排序提交的方法
        //接受id
        $id = $_POST['id'];
        //接受排序的字段
        $name = $_POST['name'];
        $type = new Type;
        $goods = new Goods;
        if($id == ''){
            $list = $goods->order($name." desc")->select();
            echo json_encode($list);
            return;
        }
        //查出该类别的子类别
        $a = $type->where("pid", $id)->select();
        if (empty($a)) {
            //如果没有子类别   直接查出goods表中typeid为该id的所有商品 返回json格式数据
            $list = $goods->where('typeid',$id)->order($name." desc")->select();
            echo json_encode($list);
            return;
        }else{
            //如果有子类别   将子类别的id拼到一起 查出goods表中typeid为该id数组的所有商品 返回json格式数据
            foreach ($a as $v) {
                $b[] = $v['id'];
            }
            $list = $goods->where("typeid","in",$b)->order($name." desc")->select();
            echo json_encode($list);
            return;
        }
    }
}