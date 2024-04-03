<?php

namespace app\common\validate;

use think\Validate;
use \think\Loader;

class Admin extends Validate
{
    protected $rule = [
        'username'     => 'require|length:4,20',
        'usernames'    => 'require|length:4,20|unique:admin,username',
        'pwd'          => 'require|length:6,18',
        'logintoken'   => 'require|checkToken',
        'pwd_o'        => 'require|length:6,18',
        'pwd_n'        => 'require|confirm:pwd_o',
        'realname'     => 'length:2,8',
        'sex'          => 'in:1,2',
        'tel'          => 'mobile',
        'email'        => 'email',
        'group_id'     => 'require|integer|gt:0',
        'id'           => 'require|integer|gt:0',
        'status'       => 'require|in:0,1|checkStatus:0,1',
    ];
    protected $message = [
        'username.require'      => '用户账号为必填项',
        'username.length'       => '用户账号格式不正确',
        'usernames.require'     => '用户账号为必填项',
        'usernames.length'      => '用户账号格式不正确',
        'usernames.unique'      => '用户账号已经存在',
        'pwd.require'           => '密码为必填项',
        'pwd.length'            => '密码格式不正确',
        'logintoken.require'    => '请通过滑块验证',
        'logintoken.checkToken' => '滑块验证已过期，请刷新重试',
        'pwd_o.require'         => '请输入新密码',
        'pwd_o.length'          => '新密码必须6-18位之间',
        'pwd_n.require'         => '请确认新密码',
        'pwd_n.confirm'         => '两次新密码不一致',
        'realname.length'       => '用户名称必须2-8位之间',
        'sex.in'                => '请选择性别',
        'tel.mobile'            => '手机号格式不正确',
        'email.email'           => '邮箱格式不正确',
        'id.require'            => '核心参数错误',
        'id.integer'            => '核心参数错误',
        'id.gt'                 => '核心参数错误',
        'group_id.require'      => '请选择一个分组',
        'group_id.integer'      => '请选择一个分组',
        'group_id.gt'           => '请选择一个分组',
        'status.require'        => '请选择状态',
        'status.in'             => '状态为必选',
        'status.checkStatus'    => '核心平台账号不能被禁用',
    ];

    protected $scene = [
        'add'      => ['usernames','pwd','realname','sex','tel','email','group_id','status'],
        'edit'     => ['id','usernames','realname','sex','tel','email','group_id','status'],
        'login'    => ['username', 'pwd', 'logintoken'],
        'password' => ['pwd', 'pwd_o', 'pwd_n'],
        'editinfo' => ['realname', 'sex', 'tel', 'email']
    ];


    protected function checkToken($value)
    {
        $ip      = sprintf("%u", ip2long(get_client_ip()));
        $captcha = authcode($ip);  // 生成32位字符串
        if (!checkToken($value, $captcha)) {
            return false;
        }
        return true;
    }

    protected function checkStatus($value, $rule, $data)
    {
        if (!empty($data['id'])) {
            if ($value == 0 and $data['id'] == 1) {
                return  $rule = false;
            }
        }
        return $rule == true;
    }
}
