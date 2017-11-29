<?php

namespace app\home\controller;
use think\Controller;
use think\Db;

class Api extends Controller
{
    public function index(){
        if(empty($_GET['city'])){
            $city = "运城";
        }else{
            $city = $_GET['city'];
        }
        $weather = $this->url("http://jisutqybmf.market.alicloudapi.com","/weather/query","16c5116c8d544fa4b5e0dbe45be0ccd9","city=".$city);
        dump($weather['result']);
    }

    public function url($host,$path,$appcode,$querys="",$bodys = "",$method = "GET"){
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        if($querys == ""){
            $url = $host . $path;
        }else{
            $url = $host . $path . "?" . $querys;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $exec = curl_exec($curl);
        if(empty($exec)){
            var_dump(curl_errno($curl));
        }else{
            header("Content-type:text/html;charset=utf-8");
            $res = json_decode($exec,true);
            return $res;
        }
    }
}