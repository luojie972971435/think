<?php

namespace app\common\validate;

use think\Validate;

class Conf extends Validate
{

    protected $rule = [
        'id'    => 'require',
        'name'  => 'require|verifyname|unique:conf',
        'title' => 'require|unique:conf',
        'type'  => 'require|integer|gt:0',
        'group' => 'require|integer|gt:0'
    ];

    protected $message = [
        'id.require'        => '核心参数错误',
        'name.require'      => '配置标识不能为空',
        'name.verifyname'   => '配置标识不符合规定',
        'name.unique'       => '配置标识不能重复',
        'title,require'     => '配置标题不能为空',
        'title.unique'      => '配置标题不能重复',
        'type.require'      => '请选择配置类型',
        'type.integer'      => '核心参数错误',
        'type.gt'           => '核心参数错误',
        'group.require'     => '请选择配置分组',
        'group.integer'     => '核心参数错误',
        'group.gt'          => '核心参数错误',
    ];

    protected $scene = [
        'add' => ['name', 'title', 'type', 'group'],
        'edit' => ['id', 'name', 'title', 'type', 'group']
    ];

    protected function verifyname($name)
    {
        if (empty($name)) {
            return false;
        }
        if (!preg_match('/^[A-Z_]+$/', $name)) {
            return false;
        } else {
            return true;
        }
    }
}
