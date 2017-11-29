<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;

use think\Db;

class Comment extends Common
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
        //数据总条数
        $totalCount = Db::table('syc_comment')->count();
        $lists = Db::query("select c.*,u.username from syc_comment c,syc_user u where c.uid = u.id order by id desc");
        //将商品信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign("list",$lists);
        return $this->fetch("index");
    }

    public function reply()
    {
        $id = input("id");
        $list = Db::table('syc_comment')->where("id",$id)->find();
        $this->assign("list",$list);
        return $this->fetch("reply");
    }

    public function save(){
        $id = input('id');
        $reply = input('reply');
        $addtime2 = time();
        $m = Db::table("syc_comment")->update(['reply'=>$reply,'addtime2'=>$addtime2,'id'=>$id]);
        if($m>0){
            $this->success("回复成功!");
        }else{
            $this->error("回复失败!");
        }
    }
}