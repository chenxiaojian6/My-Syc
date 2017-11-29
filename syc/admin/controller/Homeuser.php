<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;


use app\admin\model\User;
use think\Db;

class Homeuser extends Common
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
        $username = input('username');
        $where = array();//封装搜索条件
        $data = array('page'=>$currentPage);//维持搜索条件
        if($username){
            $where['username'] = array('like',"%{$username}%");
            $data['query']['username'] = $username;
        }
        //查询会员信息
        $list = Db::table('syc_user')->where('state','neq',3)->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);

        //数据总条数
        $totalCount = Db::table('syc_user')->where('state','neq',3)->where($where)->count();
        //将会员信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$list);
        //加载模板
        return $this->fetch("index");
    }
    public function edit($id)
    {
        $user = User::get($id);
        $this->assign('vo',$user);
        return $this->fetch('edit');
    }
    //修改会员状态
    public function update()
    {
        $id = input('id');
        $state = input('state');
        $user  = User::get($id);
        $user->state = $state;
        $m = $user->save();
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败");
        }
    }
}