<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/29
 * Time: 19:37
 */

namespace app\admin\controller;


use app\admin\model\Link as Links;
use think\Db;

class Link extends Common
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
        //查询管理员信息
        $list = Db::table('syc_link')->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);

        //数据总条数
        $totalCount = Db::table('syc_link')->where($where)->count();
        //将管理员信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('list',$list);
        //加载模板
        return $this->fetch("index");
    }
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->rule('randFileName')->move(ROOT_PATH . 'public' . DS . 'link');
        if($info){
            // 成功上传后 获取上传信息
            //获取文件路径
            $str = $info->getSaveName();
            //获取目录名
            $path = strstr($str,DS,true);

            $filename = $info->getFilename();
            return $filename;
//            $image = \think\Image::open($file);
//            $image->thumb(200,200)->save(ROOT_PATH.'public'.DS.'uploads'.DS.$path.DS."s_".$filename);
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
    public function add()
    {
        return $this->fetch('add');
    }

    public function insert()
    {
        $data['linkpic'] = $this->upload();
        $data['name'] = input('name');
        $data['linkname'] = input('linkname');
        $data['linknum'] = input('linknum');
        $link = new Links;
        $result = $link->save($data);
        if(false === $result){
            $this->error($link->getError());
        }else{
            $this->success("添加成功！");
        }
    }
    public function upload1($pic){
        // 获取表单上传文件 例如上传了001.jpg
        $file = $pic;
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->rule('randFileName')->move(ROOT_PATH . 'public' . DS . 'link');
        if($info){
            // 成功上传后 获取上传信息
            //获取文件路径
            $str = $info->getSaveName();
            //获取目录名
            $path = strstr($str,DS,true);

            $filename = $info->getFilename();
            return $filename;
//            $image = \think\Image::open($file);
//            $image->thumb(200,200)->save(ROOT_PATH.'public'.DS.'uploads'.DS.$path.DS."s_".$filename);
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }
    public function edit($id)
    {
        $link = Links::get($id);
        $this->assign('vo',$link);
        return $this->fetch('edit');
    }

    public function update()
    {
        $id = input('id');
        $list = Links::get($id);
        $data['name'] = input('name');
        $data['linkname'] = input('linkname');
        $data['linknum'] = input('linknum');
        $linkpic = request()->file('linkpic');
        if($linkpic){
            $filename = $this->upload1($linkpic);
            if($list['linkpic']){
                unlink(ROOT_PATH.'public'.DS.'link'.DS.$list['linkpic']);
            }
            $data['linkpic']=$filename;
            $m = $list->allowField(true)->save($data,$id);
            if($m>0){
                $this->success("修改成功！");
            }else{
                $this->error("修改失败！");
            }
        }else{
            $m = $list->allowField(true)->save($data,$id);
            if($m>0){
                $this->success("修改成功！");
            }else{
                $this->error("修改失败！");
            }
        }
    }

    public function del($id)
    {
        $link = Links::get($id);
        $m = $link->delete();
        if($m>0){
            if($link['linkpic']){
                unlink(ROOT_PATH.'public'.DS.'link'.DS.$link['linkpic']);
            }
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }
}