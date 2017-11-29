<?php

namespace app\home\controller;
use think\Controller;
use think\Db;
use app\home\model\User;

class Data extends Controller
{
    public function index(){
        if(session("homeuser")==null){
            return $this->redirect("/home/index/index");
            exit();
        }
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);

    	$id = session('homeuser')->id;
    	$data = User::table("syc_user")->find($id);
    	$this->assign('vo',$data);
        return $this->fetch("index");
    }

    public function edit(){
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);

    	$id = session('homeuser')->id;
    	$data = User::table("syc_user")->find($id);
    	$this->assign('vo',$data);
        return $this->fetch("edit");
    }

    public function update(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->rule('randFileName')->move(ROOT_PATH . 'public' . DS . 'headpic');
        if($info){
            // 成功上传后 获取上传信息
            //获取文件路径
			$str = $info->getSaveName();
			//获取目录名
			$path = strstr($str,DS,true);

			$filename = $info->getFilename();
			// var_dump($);die;
			// $image = \think\Image::open($file);
			// $image->thumb(80,80)->save(ROOT_PATH.'public'.DS.'headpic'.DS.$path.DS."s_".$filename);
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
	    }
	    $id = session('homeuser')->id;
    	$data = User::table("syc_user")->find($id);
    	$headpic = $data['headpic'];
	    if ($headpic !== 'headpic.jpg') {
	    	unlink(ROOT_PATH . 'public' . DS . 'headpic' . DS .$headpic);
	    }
	    $name = session('homeuser')->username;
	    $user = new User;
	    $u = $user->save(['headpic'=>$str],['username'=>$name]);
	    $id = session('homeuser')->id;
    	$data = User::table("syc_user")->find($id);
    	$headpic = $data['headpic'];
	    $this->assign('vo',$data);
        //实例化自定义方法 查出所有类别 和 友情链接
        $listsss = new Listsss();
        $list = $listsss->listsss();
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
		return $this->fetch('index');
	}

	public function updateuser(){
		$data = input("post.");
		$id = session('homeuser')->id;
		$sex = $data['sex'];
		$name = $data['name'];
		$address = $data['address'];
		$phone = $data['phone'];
		$email = $data['email'];
		$code = $data['code'];
		if (!empty($phone)) {
			if(!preg_match("/^1[34578]\d{9}$/",$phone)){
	            $this->assign('errorinfo','手机号格式不正确');
	            $date = User::table("syc_user")->find($id);
	    		$this->assign('vo',$date);
	    		$listsss = new Listsss();
       			$list = $listsss->listsss(); 
       			$links = $listsss->links();
       			$this->assign("links",$links);
       			$this->assign("list",$list);
	            return $this->fetch('edit');
	            exit();
	        }
	    }
        if (!empty($email)) {
	        if(!preg_match("/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/",$email)){
	            $this->assign('errorinfo','邮箱格式不正确');
	            $date = User::table("syc_user")->find($id);
	    		$this->assign('vo',$date);
	    		$listsss = new Listsss();
    			$list = $listsss->listsss(); 
    			$links = $listsss->links();
    			$this->assign("links",$links);
    			$this->assign("list",$list);
	            return $this->fetch('edit');
	            exit();
	        }
	    }
        if (!empty($code)) {
	        if(!preg_match("/^\d{6}$/",$code)){
	            $this->assign('errorinfo','邮政编码格式不正确');
	            $date = User::table("syc_user")->find($id);
	    		$this->assign('vo',$date);
	    		$listsss = new Listsss();
		        $list = $listsss->listsss(); 
		        $links = $listsss->links();
		        $this->assign("links",$links);
		        $this->assign("list",$list);
	            return $this->fetch('edit');
	            exit();
	        }
    	}
    	$listsss = new Listsss();
        $list = $listsss->listsss(); 
        $links = $listsss->links();
        $this->assign("links",$links);
        $this->assign("list",$list);
		$user = new User;
		$m = $user->save(['name'=>$name,'sex'=>$sex,'address'=>$address,'phone'=>$phone,'email'=>$email,'code'=>$code],['id'=>$id]);
		$date = User::table("syc_user")->find($id);
    	$this->assign('vo',$date);
        //实例化自定义方法 查出所有类别 和 友情链接
        
		return $this->fetch('index');
	}
}