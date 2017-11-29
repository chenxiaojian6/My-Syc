<?php

namespace app\home\controller;

use think\Db;

class Listsss
{
    public function listsss(){
        header("Content-type:text/html;charset=utf-8");
        $list = Db::table("syc_type")->where("pid",0)->select();
        foreach($list as $v){
            $lists[] = Db::table("syc_type")->where("pid",$v['id'])->select();
        }
        for ($i=0; $i < count($list); $i++) {
            array_push($list[$i],$lists[$i]);
        }
        return $list;
    }

    public function links(){
        $list = Db::table("syc_link")->order("linknum desc")->select();
        return $list;
    }
}