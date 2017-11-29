<?php
namespace app\admin\validate;
use think\Validate;
class Users extends Validate
{
    protected $rule = [
        'username' => 'require|regex:\w{5,12}',
        'pass' => 'require',
    ];

    protected $message = [
        'username.require' => '账号不能为空',
        'username.regex' => '账号必须是5到12位的数字、字母和下划线组成',
        'pass.require' => '密码为空',
    ];

    protected $scene = [
        'insert' => ['username','pass'],
        'update' => ['username'],
    ];
}