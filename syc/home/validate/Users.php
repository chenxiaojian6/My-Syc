<?php
namespace app\home\validate;
use think\Validate;


class Users extends Validate
{
    protected $rule = [
        'pass' => 'require',
        'email' => 'require|email',
        'age' => 'require',
    ];

    protected $message = [
        'name.require' => '手机号码不能为空',
        'name.regex' => '手机号码格式不正确',
        'pass.require' => '密码为空',
        'email.require' => '邮箱不能为空',
        'email.email' => '邮箱格式不正确',
        'age.require' => '年龄不能为空',
    ];
    protected $scene = [
        'uname'=>['username'],
    ];
}