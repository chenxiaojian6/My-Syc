<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/13
 * Time: 11:10
 */

namespace app\admin\controller;



use think\Db;
use app\admin\model\Goods as good;
use app\admin\model\Type;
use think\Image;

class Goods extends Common
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
        $name = input('goods');
        $where = array();//封装搜索条件
        $data = array('page'=>$currentPage);//维持搜索条件
        if($name){
            $where['goods'] = array('like',"%{$name}%");
            $data['query']['goods'] = $name;
        }
        //查询商品信息
        $list = Db::table('syc_goods')->where($where)->order($order,$sort)->paginate($numPerPage,false,$data);
        $a = $list->toArray();
        foreach ($a['data'] as $k=>$v) {
            $overall = Db::table('syc_goods_size')->where("gid",$v['id'])->select();
            $store = 0;
            foreach ($overall as $key => $value) {
                $store += $value['store'];
            }
            $a['data'][$k]['a'] = $store;
        }
        //数据总条数
        $totalCount = Db::table('syc_goods')->where($where)->count();
        $type = new Type;
        $lists = $type->select();
        foreach ($lists as $v){
            $value[$v['id']] = $v['name'];
        }
        //将商品信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign('type',$value);
        $this->assign('list',$a['data']);
        //加载模板
        return $this->fetch("index");
    }

    public function add()
    {
        $type = new Type;
        $list = $type->order('concat(path,id)')->select();
        foreach ($list as $k=>$v){
            $num = str_repeat("★",substr_count($v['path'],",")-1);
            $list[$k]['name'] = $num.$v['name'];
            $disabled = $type->where('pid',$v['id'])->select();
            if($disabled){
                $list[$k]['disabled']=0;
            }else{
                $list[$k]['disabled']=1;
            }
        }
        $this->assign("type",$list);
        return $this->fetch('add');
    }

    public function uploads(){
        // 获取表单上传文件
        $files = request()->file('image');
        $data = array();
        $m=1;
        foreach($files as $file){
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->rule('randFileName')->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                //获取文件路径
                $str = $info->getSaveName();

                //获取目录名
                $path = strstr($str,DS,true);

                $filename = $info->getFilename();
                $data['picname'.$m]=$filename;
                $m++;
//                $image = \think\Image::open($file);
//                $image->thumb(200,200)->save(ROOT_PATH.'public'.DS.'uploads'.DS.$path.DS."s_".$filename);
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        return $data;
    }

    public function insert()
    {
        $data = $this->uploads();
        $data['typeid'] = input("typeid");
        $data['goods'] = input("goods");
        $data['company'] = input("company");
        $data['price'] = input("price");
        $data['state'] = input("state");
        $goods = new Good;
        $list = $goods->allowField(true)->save($data);
        if ($list>0) {
            $this->success("添加成功!");
        } else {
            $this->error('添加失败');
        }
    }

    public function edit($id)
    {
        $type = new Type;
        $list = $type->order('concat(path,id)')->select();
        foreach ($list as $k=>$v){
            $num = str_repeat("★",substr_count($v['path'],",")-1);
            $list[$k]['name'] = $num.$v['name'];
            $disabled = $type->where('pid',$v['id'])->select();
            if($disabled){
                $list[$k]['disabled']=0;
            }else{
                $list[$k]['disabled']=1;
            }
        }
        $this->assign("type",$list);
        $goods = Good::get($id);
        $this->assign('vo',$goods);
        return $this->fetch('edit');
    }

    public function upload($pic){
        // 获取表单上传文件 例如上传了001.jpg
        $file = $pic;
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->rule('randFileName')->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            //获取文件路径
            $str = $info->getSaveName();
            //获取目录名
            $path = strstr($str,DS,true);

            $filename = $info->getFilename();
            return $filename;
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
    }

    public function update()
    {
        $id = input('id');
        $list = Good::get($id);
        $picname1 = request()->file('picname1');
        if($picname1){
            $filename = $this->upload($picname1);
            if($list['picname1']){
                unlink(ROOT_PATH.'public'.DS.'uploads'.DS.$list['picname1']);
            }
            $data['picname1']=$filename;
        }
        $picname2 = request()->file('picname2');
        if($picname2){
            $filename = $this->upload($picname2);
            if($list['picname2']){
                unlink(ROOT_PATH.'public'.DS.'uploads'.DS.$list['picname2']);
            }
            $data['picname2']=$filename;
        }
        $picname3 = request()->file('picname3');
        if($picname3){
            $filename = $this->upload($picname3);
            if($list['picname3']){
                unlink(ROOT_PATH.'public'.DS.'uploads'.DS.$list['picname3']);
            }
            $data['picname3']=$filename;
        }
        $data['typeid'] = input("typeid");
        $data['goods'] = input("goods");
        $data['company'] = input("company");
        $data['price'] = input("price");
        $data['state'] = input("state");
        $data['num'] = input("num");
        $data['clicknum'] = input("clicknum");
        $m = $list->save($data,$id);
        if($m>0){
            $this->success("修改成功！");
        }else{
            $this->error("修改失败");
        }
    }

    public function del($id){
        $list = Db::table("syc_goods")->where("id",$id)->find();
        $m = Db::table("syc_goods")->where('state','neq',2)->where("id",$id)->delete();
        Db::table("syc_goods_size")->where("gid",$id)->delete();
        Db::table("syc_comment")->where("goodsid",$id)->delete();
        if($m>0){
            if($list['picname1']){
                unlink(ROOT_PATH.'public'.DS.'uploads'.DS.$list['picname1']);
            }
            if($list['picname2']){
                unlink(ROOT_PATH.'public'.DS.'uploads'.DS.$list['picname2']);
            }
            if($list['picname3']){
                unlink(ROOT_PATH.'public'.DS.'uploads'.DS.$list['picname3']);
            }
            $this->success("删除成功！");
        }else{
            $this->error("删除失败！");
        }
    }

    public function chima()
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
        $id = input("gid");
        //数据总条数
        $totalCount = Db::table('syc_goods_size')->where('gid',$id)->count();
        $lists  = Db::query("select g.goods,gs.id,gs.store,gs.gid,s.name from syc_goods_size gs,syc_goods g,syc_size s where g.id = gs.gid and gs.sid = s.id and g.id = ".$id);

        //将商品信息放置到模板中
        $this->assign('currentPage',$currentPage);
        $this->assign('numPerPage',$numPerPage);
        $this->assign('totalCount',$totalCount);
        $this->assign("list",$lists);
        return $this->fetch("size");
    }
}