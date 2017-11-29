<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/22
 * Time: 20:23
 */

namespace app\admin\controller;


use think\Db;
use think\Request;

class Size extends Common
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
        //封装排序
        $order = input('_order');
        if(empty($order)){
            $order = 'id';
        }
        $sort = input('_sort');
        if(empty($sort)){
            $sort = 'desc';
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
        //查询商品信息
        $list = Db::table('syc_size')->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);
        //数据总条数
        $totalCount = Db::table('syc_size')->where($where)->count();
        //将商品信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$list);
        //加载模板
        return $this->fetch("index");
    }
    public function add()
    {
        return $this->fetch("add");
    }
    public  function  insert()
    {
        $data['name'] = input('name');
        $m = Db::table('syc_size')->insert($data);
        if($m>0){
            $this->success("添加成功！");
        }else{
            $this->error("添加失败！");
        }
    }
    public function edit($id)
    {
        $size = Db::table("syc_size")->find($id);
        $this->assign("vo",$size);
        return $this->fetch("edit");
    }

    public function update()
    {
        //获取请求对象
        $request = Request::instance();
        //获取修改信息
        $data = $request->post();
        //执行修改
        $m = Db::table("syc_size")->update($data);
        //判断
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败！");
        }
    }
    public function del($id)
    {
        $m = Db::table("syc_size")->delete($id);
        //判断
        if($m>0){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }
    public function sizelist($gid=0)
    {
        //获取当前商品信息
        $goods = Db::table('syc_goods')->find($gid);
        //获取所有的尺码信息
        $sizelist = Db::table('syc_size')->select();
        //获取当前商品的尺码信息
        $data = Db::table('syc_goods_size')->where('gid',$gid)->select();
        //获取节点id
        $sids = array();
        foreach($data as $v){
            $sids[] = $v['sid'];
        }
        //将变量放置到模板
        $this->assign('goods',$goods);
        $this->assign('sizelist',$sizelist);
        $this->assign('sids',$sids);
        //加载模板
        return $this->fetch('sizelist');
    }

    public function savesize()
    {
        //获取当前商品的id
        $data['gid'] = input('gid');
        $data['sid'] = input('sizeid');
        $data['store'] = input('store');
        $list = Db::table("syc_goods_size")->insert($data);
        if($list>0){
            $this->success("添加成功!");
        }else{
            $this->success("添加失败!");
        }
    }

    public function sizeedit($id)
    {
        //获取所有的尺码信息
        $sizelist = Db::table('syc_size')->select();
        $lists  = Db::table('syc_goods_size')->find($id);
        //将变量放置到模板
        $this->assign('sizelist',$sizelist);
        $this->assign('lists',$lists);
        //加载模板
        return $this->fetch('sizeedit');
    }

    public function edit2(){
        $data['sid'] = input("sizeid");
        $data['store'] = input("store");
        $id = input("id");
        $m = Db::table('syc_goods_size')->where('id',$id)->update($data);
        if($m>0){
            $this->success("修改成功!");
        }else{
            $this->error("修改失败!");
        }
    }

    public function del1($id)
    {
        $m = Db::table("syc_goods_size")->delete($id);
        //判断
        if($m>0){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }
}