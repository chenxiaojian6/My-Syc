<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/26
 * Time: 20:25
 */

namespace app\admin\controller;


use think\Db;
class Orders extends Common
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
        $name = input('orderid');
        $where = array();//封装搜索条件
        $data = array('page'=>$currentPage);//维持搜索条件
        if($name){
            $where['orderid'] = array('like',"%{$name}%");
            $data['query']['orderid'] = $name;
        }
        //查询订单信息
        $list = Db::table('syc_orders')->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);
        //数据总条数
        $totalCount = Db::table('syc_orders')->where($where)->count();
        //将订单信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$list);
        return $this->fetch('index');
    }
    public function  send()
    {
        $id = $_POST['id'];
        $m = Db::table("syc_orders")->where('id',$id)->update(['status'=>2]);
        if($m>0)
        {
            return true;
        }

    }
}