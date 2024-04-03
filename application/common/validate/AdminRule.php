<?php

namespace app\common\validate;

use think\Validate;
use \think\Loader;

class AdminRule extends Validate
{

    protected $rule = [
        'id'          => 'require',
        'title'       => 'require|unique:admin_rule',
        'pid'         => 'require|integer|egt:0',
        'status'      => 'in:0,1'

    ];

    protected $message = [
        'id.require'        => '核心参数错误',
        'pid.require'       => '缺少父级节点参数',
        'pid.integer'       => '父级节点参数错误',
        'pid.egt'           => '父级节点参数错误',
        'title.require'     => '节点名称不能为空',
        'title.unique'      => '此节点名称已存在',
        'status.in'         => '核心参数错误'
    ];

    protected $scene = [
        'add'      => ['title', 'pid', 'status'],
        'edit'     => ['id','title', 'pid', 'status']
    ];
}
