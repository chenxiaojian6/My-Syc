<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    //定义路由
    'index' => 'home/index/index',//首页
    'login' => 'home/login/index',//登录
    'reg' => 'home/reg/index',//注册
    'list/:id' => 'home/lists/index',//列表页
    'detail/:id' => 'home/detail/index',//详情页
    'per' => 'home/per/index',//个人中心
    'orders/:id' => 'home/orders/index2',//订单详情
    'comment/:goodsid/:id' => 'home/comment/index',//评论
    'orders' => 'home/orders/index',//我的订单
    'data' => 'home/data/index',//个人资料
    'address' => 'home/address/index',//收货地址
    'add_address' => 'home/address/add',//新增收货地址
    'edit_address/:id' => '/home/address/edit',//修改收货地址
    'del_address/:id' => '/home/address/del',//删除收货地址
    'pass' => 'home/pass/index',//修改密码
    'collect' => 'home/collect/index',//我的收藏
    'del_collect/:id' => 'home/collect/del',//删除收藏
    'add_collect/:id' => 'home/collect/add',//添加收藏
    'car' => 'home/car/index',//购物车
];
