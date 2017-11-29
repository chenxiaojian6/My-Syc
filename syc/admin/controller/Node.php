<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;



use think\Db;
use think\Request;

class Node extends Common
{


    //浏览学生信息
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
        //查询学生信息
        $list = Db::table('syc_node')->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);

        //数据总条数
        $totalCount = Db::table('syc_node')->where($where)->count();
        //将学生信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$list);
        //加载模板
        return $this->fetch("index");

    }





    //添加学生信息表单
    public function add()
    {
        return $this->fetch("add");
    }

    //执行添加学生信息
    public function insert()
    {
        //创建请求对象
        $request = Request::instance();
        //获取post提交数据
        $data = $request->post();
        //执行添加
        $m = Db::table("syc_node")->insert($data);
        //判断
        if($m>0){
            $this->success("添加成功！");
        }else{
            $this->error("添加失败！");
        }
    }

    //执行删除学生信息
    public function del($id)
    {
        $m = Db::table("syc_node")->delete($id);
        //判断
        if($m>0){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }

    //加载修改学生信息表单
    public function edit($id)
    {
        //获取学生信息
        $stu = Db::table("syc_node")->find($id);
        //将要修改的学生信息放置到模板中
        $this->assign("vo",$stu);
        //加载模板
        return $this->fetch("edit");
    }

    //执行修改学生信息
    public function update()
    {
        //获取请求对象
        $request = Request::instance();
        //获取修改信息
        $data = $request->post();
        //执行修改
        $m = Db::table("syc_node")->update($data);
        //判断
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败！");
        }
    }
}