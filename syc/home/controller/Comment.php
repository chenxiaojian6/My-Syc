<?php
namespace app\home\controller;
use app\home\model\Goods;
use think\Controller;
use think\Db;

class Comment extends Controller
{
    public function index(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);

        $id = input("id");
        $gid = input("gid");
        $goods = new Goods();
        $lists = $goods->where("id",$gid)->find();
        $this->assign("id",$id);
        $this->assign("lists",$lists);
        return $this->fetch("index");
    }

    public function uploads(){
        // 获取表单上传文件
        $files = request()->file('img');
        $data = array();
        $m=1;
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->rule('randFileName')->move(ROOT_PATH . 'public' . DS . 'comment');
            if($info){
                //获取文件路径
                $str = $info->getSaveName();

                //获取目录名
                $path = strstr($str,DS,true);

                $filename = $info->getFilename();
                $data['picname'.$m]=$filename;
                $m++;
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        return $data;
    }

    public function add(){
        $data = $this->uploads();
        $id = input("id");
        $data['comment'] = input("comment");
        $data['goodsid'] = input("goodsid");
        $data['addtime1'] = time();
        $data['uid'] = session("homeuser")->id;
        $list = Db::table("syc_comment")->insert($data);
        if ($list>0) {
            Db::table("syc_detail")->where("id",$id)->update(['state'=>2]);
            echo "<script>alert('评论成功!');window.location.href='http://www.xiaojian716.club/orders.html';</script>";
        } else {
            $this->error('评论失败!');
        }
    }
}