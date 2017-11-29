<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
// use app\home\model\User;

class Collect extends Controller
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
        //遍历该用户收藏的商品
        $id = session('homeuser')->id;
        $link = Db::table('syc_collect')->where('uid',$id)->order("id desc")->select();
        $this->assign("link",$link);
        return $this->fetch("index");
    }

    public function add(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        if(session("homeuser")==null){
            echo "<script>alert('请登录!');window.location.href='http://www.xiaojian716.club/login.html';</script>";
            exit;
        }
        //获取用户id
        $uid = session('homeuser')->id;
        //接收商品的id
        $goodid = input('id');
        //查出该商品的信息
        $listt = Db::table('syc_goods')->where('id',$goodid)->find();
        //查找该用户是否已收藏该商品
        $lis = Db::table('syc_collect')->where('goodid',$goodid)->where('uid',$uid)->find();
        if ($lis) {
            echo "<script>alert('已收藏!!');window.location.href='http://www.xiaojian716.club/detail/$goodid.html';</script>";
            exit;
        }else{
            //收藏
            $goods = $listt['goods'];
            $picname = $listt['picname1'];
            $price = $listt['price'];
            $time = time();
            $data = ['uid' => $uid, 'goodid' => $goodid, 'goods' => $goods, 'picname' => $picname, 'price' => $price, 'time' => $time];
            $m = Db::table('syc_collect')->insert($data);
            if ($m) {
                echo "<script>alert('收藏成功!!');window.location.href='http://www.xiaojian716.club/detail/$goodid.html';</script>";
            }
        }
    }

    public function del(){
        $id = input('id');
        $m = Db::table('syc_collect')->where('id',$id)->delete();
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        $id = session('homeuser')->id;
        $link = Db::table('syc_collect')->where('uid',$id)->select();
        $this->assign("link",$link);
        if ($m) {
            echo "<script>alert('删除成功!!');</script>";
            return $this->fetch('index');
        }
    }
}