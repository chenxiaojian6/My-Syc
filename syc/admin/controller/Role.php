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

class Role extends Common
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
        $list = Db::table('syc_role')->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);

        //数据总条数
        $totalCount = Db::table('syc_role')->where($where)->count();
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
        $m = Db::table("syc_role")->insert($data);
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
        $m = Db::table("syc_role")->delete($id);
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
        $stu = Db::table("syc_role")->find($id);
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
        $m = Db::table("syc_role")->update($data);
        //判断
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败！");
        }
    }

    //浏览当前角色的节点信息
    public function nodelist($rid=0)
    {
        //获取当前角色信息
        $role = Db::table('syc_role')->find($rid);
//        dump($user);die;
        //获取所有的节点信息
        $nodelist = Db::table('syc_node')->select();
//        dump($rolelist);die;
        //获取当前角色的节点信息
        $data = Db::table('syc_role_node')->where('rid',$rid)->select();
//        dump($data);die;
        //获取节点id
        $nids = array();
        foreach($data as $v){
            $nids[] = $v['nid'];
        }
//dump($rids);die;
        //将变量放置到模板
        $this->assign('role',$role);
        $this->assign('nodelist',$nodelist);
        $this->assign('nids',$nids);
        //加载模板
        return $this->fetch('nodelist');
    }

    //执行节点信息的保存
    public function savenode()
    {
        //获取当前角色的id
        $rid = input('rid');
        //删除当前角色的节点信息
        Db::table('syc_role_node')->where('rid',$rid)->delete();

        //获取选择的节点信息
        $param = request()->param();
        //判断选择节点信息是否为空
        if(!empty($param['nid'])){
            //将当前选择的节点信息添加
            $nid = $param['nid'];
            $data = array();
            foreach($nid as $k=>$v){
                $data[$k]['rid'] = $rid;
                $data[$k]['nid'] = $v;
            }
            $m = Db::table('syc_role_node')->insertAll($data);
            //判断
            if($m>0){
                $this->success('添加节点成功！');
            }else{
                $this->error('添加节点失败！');
            }
        }else{
            $this->error('您没有选择节点信息');
        }
    }
}