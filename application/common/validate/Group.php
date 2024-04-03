<?php
namespace app\common\validate;
use think\Validate;
use \think\Loader;

class Group extends Validate {

    protected $rule = [
        'id'          => 'require',
        'title'       => 'require|unique:admin_group',
        'status'      => 'require|in:0,1|checkStatus:0,1'
    ];

    protected $message = [
        'id.require'          => '核心参数错误',
        'title.require'       => '角色名称不能为空',
        'title.unique'        => '角色名称已经存在',
        'status.require'      => '请选择状态',
        'status.in'           => '请选择状态',
        'status.checkStatus'  => '核心平台角色不能被禁用',
    ];

    protected $scene = [
        'add'  => ['title','status'],
        'edit' => ['id', 'title', 'status'],
    ];

    protected function checkStatus($value,$rule,$data)
    {   
        if (!empty($data['id'])) {
            if($value == 0 and $data['id'] == 1) {
                return  $rule = false;
            }
        }
        return $rule = true;
    }
}