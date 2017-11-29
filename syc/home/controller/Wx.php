<?php
namespace app\home\controller;
use think\Controller;
use JsSDK\JsSDK;

class Wx extends Controller
{
    public function index(){
        // $signature = $_GET["signature"];
        // $timestamp = $_GET["timestamp"];
        // $nonce = $_GET["nonce"];
        // $echostr = $_GET['echostr'];

        // $token = 'weixin';
        // $tmpArr = array($token, $timestamp, $nonce);
        // // use SORT_STRING rule
        // sort($tmpArr, SORT_STRING);
        // $tmpStr = implode($tmpArr);
        // $tmpStr = sha1($tmpStr);

        // if($tmpStr == $signature && $echostr){
        //     echo $echostr;
        //     exit;
        // }
        $js = new JsSDK("wx97826401fd5bc966","8de1330cd481d86d324ee1b1e10e7ebb");
        $a = $js->GetSignPackage();
        $this->assign("list",$a);
        return $this->fetch("index");
    }

    //自定义菜单
    public function setMenu()
    {
        $accesstoken = $this->getWxAccessToken();
        var_dump($accesstoken);
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$accesstoken;
        $data = array(
                    'button'=>array(
                            array(
                                'type'=>'click',
                                'name'=>urlencode('菜单一'),
                                'key'=>'item1',
                            ),//第一个一级菜单
                            array(
                                'name'=>urlencode('菜单二'),
                                'sub_button'=>array(
                                    array(
                                        'type'=>'click',
                                        'name'=>urlencode('菜单二子菜单一'),
                                        'key'=>'item2_item1',
                                    ),//菜单二的第一个子菜单
                                    array(
                                        'type'=>'view',
                                        'name'=>urlencode('菜单二子菜单二'),
                                        'url'=>'http://www.baidu.com',
                                    ),
                                ),
                            ),//第二个一级菜单
                            array(
                                'name'=>urlencode('菜单三'),
                                'sub_button'=>array(
                                    array(
                                        'type'=>'click',
                                        'name'=>urlencode('菜单三子菜单一'),
                                        'key'=>'item3_item1',
                                    ),//菜单三的第一个子菜单
                                    array(
                                        'type'=>'view',
                                        'name'=>urlencode('菜单三子菜单二'),
                                        'url'=>'http://www.qq.com',
                                    ),
                                ),
                            ),//第三个一级菜单
                        )
                );

        $data = urldecode(json_encode($data));

        $output = $this->http_curl($url,'post',$data);
        var_dump($output);

    }

    public function  getUserOpenId()
    {
        $accesstoken = $this->getWxAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$accesstoken;
        $output = $this->http_curl($url);
        $arr = json_decode($output,true);
        return $arr['data']['openid'][0];
    }

    //群发消息（预览）
    public function sendMsgAll()
    {
        $accesstoken = $this->getWxAccessToken();
        $openid = $this->getUserOpenId();
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accesstoken;
        $data = "{
                    \"touser\":\"{$openid}\",
                    \"template_id\":\"mhlMh1V_GpwGrTWk-1M7t4nHRuxIiTyopLUPk2cctEE\",
                    \"url\":\"http://www.baidu.com/\",
                    \"topcolor\":\"#FF0000\",
                    \"data\":{
                        \"first\": {
                            \"value\":\"恭喜你购买成功！\",
                            \"color\":\"#173177\"
                        },
                        \"keyword1\":{
                            \"value\":\"阿西吧\",
                            \"color\":\"#173177\"
                        },
                        \"keyword2\":{
                            \"value\":\"36856829322344\",
                            \"color\":\"#173177\"
                        },
                        \"keyword3\":{
                            \"value\":\"3999\",
                            \"color\":\"#173177\"
                        },
                        \"keyword4\":{
                            \"value\":\"2017-10-1\",
                            \"color\":\"#173177\"
                        },
                        \"remark\":{
                            \"value\":\"欢迎再次购买！\",
                            \"color\":\"#173177\"
                        }
                    }
                }";
        $output = $this->http_curl($url,'post',$data);
        var_dump($output);
    }

    //返回access_token,这里用session解决方案，也可以用memcache,redis,mysql
    public function getWxAccessToken()
    {
        // if(session('access_token') && session('expire_time')>time()){
        //     return session('access_token');
        // }else{
            //请求url地址
            $appid = 'wx97826401fd5bc966';
            $appsecret = '8de1330cd481d86d324ee1b1e10e7ebb';
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret;
            $res = $this->http_curl($url);
            $arr = json_decode($res,true);
            //将获取到的access_token放入session中
            session('access_token',$arr['access_token']);
            session('expire_time',time()+7000);
            return $arr['access_token'];
        // }
    }

    /**
    *   $url 接口url string
    *   $type 请求类型 string get/post
    *   $arr post请求数据，string
    *   $res 返回数据类型 string
    */

    public function http_curl($url,$type='get',$arr='',$res='json')
    {
        //初始化curl
        $ch = curl_init();
        //设置curl参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        if($type=='post'){
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }

        //采集信息
        $output = curl_exec($ch);

        //返回值
        if(curl_error($ch)){
            return curl_error($ch);
        }else{
            return $output;
        }
        //关闭
        curl_close($ch);
    }
}