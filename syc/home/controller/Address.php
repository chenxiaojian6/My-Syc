<?php

namespace app\home\controller;
use app\home\model\Address as Add;
use think\Controller;
use think\Db;

class Address extends Controller
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
        $this->assign("lists",$list);

        $id = session("homeuser")->id;
        $address = new Add();
        $list = $address->where('uid',$id)->order('state')->select();
        $this->assign("list",$list);
        return $this->fetch("index");
    }

    public function add(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        $url = url("/home/address/index");
        $this->assign("url",$url);
        return $this->fetch("add");
    }

    public function insert(){
        $address = new Add();
        $data['name'] = input("name");
        $data['address'] = input("address");
        $data['phone'] = input("phone");
        $data['code'] = input("code");
        $data['uid'] = session("homeuser")->id;
        $list = $address->save($data);
        if($list>0){
            return true;
        }else{
            return false;
        }
    }

    public function edit(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("lists",$list);
        $id = input("id");
        $address = Add::get($id);
        $add = explode("|",$address['address'])['1'];
        $this->assign("add",$add);
        $this->assign("list",$address);
        return $this->fetch("edit");
    }

    public function update(){
        $data = $_POST;
        $address = new Add();
        $list = $address->save($data,$data['id']);
        if($list>0){
            return true;
        }else{
            return false;
        }
    }

    public function del(){
        $id = input("id");
        $address = Add::get($id);
        $m = $address->delete();
        if($m>0){
            return $this->redirect("/home/address/index");
        }else{
            $this->error('删除失败');
        }
    }

    public function defa(){
        $id = $_POST['id'];
        $uid = session("homeuser")->id;
        $address = new Add;
        $list = $address->where("uid",$uid)->select();
        foreach ($list as $v){
            if($v['state'] == 1){
                Db::execute('update syc_address set state=2 where id=?',[$v['id']]);
            }
        }
        $a = Db::execute('update syc_address set state=1 where id='.$id);
        if($a>0){
            return true;
        }else{
            return false;
        }
    }

    public function address(){
        $upid = isset($_GET['upid'])?$_GET['upid']:0;
        $list = Db::query('select * from syc_district where upid=?',[$upid]);
        echo json_encode($list);
    }
    public function edit2(){

        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
        $id = input("id");
        $address = Add::get($id);
        $add = explode("|",$address['address'])['1'];
        $this->assign("add",$add);
        $this->assign("list",$address);
        return $this->redirect("/home/order/index");
    }
}