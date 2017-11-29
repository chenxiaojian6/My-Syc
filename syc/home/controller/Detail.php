<?php

namespace app\home\controller;
use think\Controller;
use think\Db;
use app\home\model\Goods;
use app\home\model\Type;

class Detail extends Controller
{
    public function index(){
        //接收id
        $id = input("id");
		if(empty($id)){
			abort(404,'页面不存在');
		}
        $goods = new Goods;
        //查出总库存
        $data = Db::query("select gs.store from syc_goods_size gs where gs.gid=".$id);
        $store =0;
        foreach ($data as $value){
            $store +=$value['store'];
        }
        $storenum = Db::table("syc_goods")->where('id',$id)->update(['store'=>$store]);
        //查出该商品尺码
        $lists  = Db::query("select s.name,gs.store from syc_goods g,syc_goods_size gs,syc_size s where gs.gid = ".$id." and g.id = ".$id." and gs.sid = s.id");
        //将该商品的信息查出来
        $glist = $goods->where("id",$id)->find();
        //将点击量加一
        $clicknum = $glist->clicknum+1;
        $goods->where("id",$id)->update(['clicknum'=>$clicknum]);
        $price = ceil($glist->price/0.9);
        $this->assign("price",$price);
        $this->assign("glist",$glist);
        $this->assign("lists",$lists);
        $this->assign("id",$id);
        //查出所有的根类别
        $type = new Type;
        $tlist = $type->where("pid",0)->select();
        $this->assign("tlist",$tlist);
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        //查出该商品的所有评论
        $comment = Db::query("select c.*,u.username,u.headpic from syc_comment c,syc_user u where c.uid = u.id and goodsid = ".$id);
        $this->assign("comment",$comment);
        return $this->fetch("index");
    }

    public function num(){
        $size = $_POST['size'];
        $number = Db::table("syc_size")->where('name',$size)->find();
        $sid = $number['id'];
        $gid = $_POST['gid'];
        $number = Db::table("syc_goods_size")->where('gid',$gid)->where('sid',$sid)->find();
        $store = (int)$number['store'];
        return $store;
    }

    public function buy(){
        if(session("homeuser")){
            return true;
        }else{
            return false;
        }
    }

    public function car(){
        if(session("homeuser")){
            $uid = session('homeuser')->id;
            $gid = $_POST['gid'];
            $number = $_POST['num'];
            $size = $_POST['size'];
            $list = Db::table("syc_goods")->where("id",$gid)->find();
            $data['uid'] = $uid;
            $data['gid'] = $gid;
            $data['goods'] = $list['goods'];
            $data['price'] = $list['price'];
            $data['picname1'] = $list['picname1'];
            $data['number'] = $number;
            $data['size'] = $size;
            //查出该用户购物车所有的商品信息和本次添加的商品id是否重复
            $usercar = Db::table("syc_car")->where("gid",$gid)->where('uid',$uid)->select();
            if($usercar){
                //查询该用户购物车中所有的商品信息和本次添加的商品id 尺码 是否重复
                $goodssize = Db::table("syc_car")->where("gid",$gid)->where('uid',$uid)->where("size",$size)->find();
                if($goodssize){
                    //查出尺码的id
                    $numb = Db::table("syc_size")->where('name',$size)->find();
                    $sid = $numb['id'];
                    //查出该尺码 该商品的库存
                    $gnum = Db::table("syc_goods_size")->where("gid",$gid)->where("sid",$sid)->find();
                    $goodsnum = (int)$gnum['store'];
                    $goodsnum2 = (int)$goodssize['number'];
                    //判断库存
                    if($goodsnum2 == $goodsnum){
                        return "库存不足";
                    }else if($goodsnum2+$number > $goodsnum){
                        return "库存不足";
                    }else{
                        //加入购物车
                        $data['number']=$goodssize['number']+$number;
                        $car = Db::table("syc_car")->update(["number"=>$data['number'],"id"=>$goodssize['id']]);
                        if($car>0)
                        {
                            $m = (int)$number;
                            return $m;
                        }
                    }
                }else{
                    //如果商品重复 尺码不重复 则直接加入购物车
                    $insertid = Db::table("syc_car")->insertGetId($data);
                    $lists= Db::table("syc_car")->where("id",$insertid)->find();
                    if($lists['number']>0){
                        $m = (int)$lists['number'];
                        return $m;
                    }
                }
            }else{
                //如果商品 尺码 不重复 则直接加入购物车
                $insertid = Db::table("syc_car")->insertGetId($data);
                $lists= Db::table("syc_car")->where("id",$insertid)->find();
                if($lists['number']>0){
                    $m = (int)$lists['number'];
                    return $m;
                }
            }
        }else{
            return false;
        }
    }
}