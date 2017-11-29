<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;



use app\admin\model\User as Users;
use think\Db;

class User extends Common
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
        //查询管理员信息
        $list = Db::table('syc_user')->where('state',3)->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);

        //数据总条数
        $totalCount = Db::table('syc_user')->where('state',3)->where($where)->count();
        //将管理员信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$list);
        //加载模板
        return $this->fetch("index");
    }

    public function add()
    {
        return $this->fetch('add');
    }

    public function insert()
    {
        $data['username'] = input('username');
        $data['pass'] = md5(input('pass'));
        $name = input('username');
        $user = new Users;
        $list = $user->where('username',$name)->where('state',3)->select();
        if(!$list) {
            $result = $user->save($data);
            if (false === $result) {
                $this->error($user->getError());
            } else {
                $this->success("添加成功！");
            }
        }else{
            $this->error("该用户已存在!");
        }
    }

    public function edit($id)
    {
        $data = session('adminuser');
        if($data['username'] != "admin"){
            $this->error("您没有此权限!");
        }else{
            $user = Users::get($id);
            $data = $user->where("id",$id)->find();
            $this->assign('vo',$data);
            return $this->fetch('edit');
        }
    }

    public function update()
    {
        $pass = md5(input('pass'));
        $id = input('id');
        $user  = Users::get($id);
        $user->pass = $pass;
        $m = $user->save();
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败");
        }
    }

    public function del($id)
    {
        if($id == session('adminuser')->id){
            $this->error("当前用户不可删除！");
        }
        $user = Users::get($id);
        $m = $user->delete();
        if($m>0){
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }

    //浏览当前用户的角色信息
    public function rolelist($uid=0)
    {
        //获取当前用户信息
        $user = Db::table('syc_user')->find($uid);
//        dump($user);die;
        //获取所有的角色信息
        $rolelist = Db::table('syc_role')->select();
//        dump($rolelist);die;
        //获取当前用户的角色信息
        $data = Db::table('syc_users_role')->where('uid',$uid)->select();
//        dump($data);die;
        //获取角色id
        $rids = array();
        foreach($data as $v){
            $rids[] = $v['rid'];
        }
//dump($rids);die;
        //将变量放置到模板
        $this->assign('user',$user);
        $this->assign('rolelist',$rolelist);
        $this->assign('rids',$rids);
        //加载模板
        return $this->fetch('rolelist');
    }

    //执行角色信息的保存
    public function saverole()
    {
        //获取当前用户的id
        $uid = input('uid');
        //删除当前用户的角色信息
        Db::table('syc_users_role')->where('uid',$uid)->delete();

        //获取选择的角色信息
        $param = request()->param();
        //判断选择角色信息是否为空
        if(!empty($param['rid'])){
            //将当前选择的角色信息添加
            $rid = $param['rid'];
            $data = array();
            foreach($rid as $k=>$v){
                $data[$k]['uid'] = $uid;
                $data[$k]['rid'] = $v;
            }
            $m = Db::table('syc_users_role')->insertAll($data);
            //判断
            if($m>0){
                $this->success('分配角色成功！');
            }else{
                $this->error('分配角色失败！');
            }
        }else{
            $this->error('您没有选择角色信息');
        }
    }
}