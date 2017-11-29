<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/13
 * Time: 11:10
 */

namespace app\admin\controller;



use think\Db;
use app\admin\model\Type as Types;

class Type extends Common
{
    public function index()
    {
        //第几页
        $currentPage = input('pageNum');
        if(empty($currentPage)){
            $currentPage = 1;
        }
        //页大小
        $numPerPage = input('numPerPage');
        if(empty($numPerPage)){
            $numPerPage = 5;
        }

        //封装搜索条件
        //接收表单数据信息
        $name = input('name');
        $where = array();//封装搜索条件
        $data = array('page'=>$currentPage);//维持搜索条件
        if($name){
            $where['name'] = array('like',"%{$name}%");
            $data['query']['name'] = $name;
        }
        //查询学生信息
        $list = Db::table('syc_type')->where($where)->order('concat(path,id)')->paginate($numPerPage,false,$data);
        foreach ($list as $value) {
            if($value['path'] === '0,'){
                $name = $value['name'];
                $value['name'] = $name;
            }else{
                $num = str_repeat("★",substr_count($value['path'],",")-1);
                $name = $num.$value['name'];
                $value['name'] = $name;
            }
            $a[] = $value;
        }
        if(empty($a)){
            $a = "";
        }
        //数据总条数
        $totalCount = Db::table('syc_type')->where($where)->count();
        //将学生信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$a);
        //加载模板
        return $this->fetch("index");
    }

    public function add()
    {
        $id = input('id');
        $type = new Types();
        if(empty($id)){
            $fname = "根类别";
            $this->assign('id','');
        }else{
            $list = $type->where("id",$id)->find();
            $fname = $list['name'];
            $this->assign('id',$id);
        }
        $this->assign('name',$fname);
        return $this->fetch('add');
    }

    public function insert()
    {
        $name = input('name');
        $id = input('id');
        $type = new Types;
        $list = $type->where("name",$name)->find();
        if(!$list){
            if(empty($id)){
                $type->name = $name;
                $type->pid = "0";
                $type->path = "0,";
                $a = $type->save();
                if($a>0){
                    $this->success("添加成功!","index");
                }else{
                    $this->error("添加失败!");
                }
            }else{
                $list1 = $type->where("id", $id)->find();
                $type->name = $name;
                $type->pid = $list1['id'];
                $type->path = $list1['path'].$list1['id'].",";
                $a = $type->save();
                if($a>0){
                    $this->success("添加成功!","index");
                }else{
                    $this->error("添加失败!");
                }
            }
        }else{
            $this->error('该类别已存在!');
        }
    }

    public function edit($id)
    {
        $type = Types::get($id);
        $this->assign('vo',$type);
        return $this->fetch('edit');
    }

    public function update()
    {
        $data = input('post.');
        $type = new Types;
        $m = $type->save($data,$data['id']);
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败");
        }
    }

    public function del($id){
        $list = Types::where("pid",$id)->select();
        if(!$list){
            $goods = Db::table("syc_goods")->where("typeid",$id)->select();
            if(!$goods){
                $type = Types::get($id);
                $m = $type->delete();
                if($m>0){
                    $this->success("删除成功！");
                }else{
                    $this->error("删除失败！");
                }
            }else{
                $this->error("该类别下有商品,请先删除商品!");
            }
        }else{
            $this->error("该类别有子类别,请先删除子类别!");
        }
    }
}