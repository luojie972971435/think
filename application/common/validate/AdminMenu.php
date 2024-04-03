<?php

namespace app\common\validate;

use think\Validate;
use \think\Loader;

class AdminMenu extends Validate
{

    protected $rule = [
        'id'          => 'require',
        'title'       => 'require|unique:admin_menu',
        'pid'         => 'require|integer|egt:0',
        'status'      => 'in:0,1',
        'sort'        => 'require|integer|egt:0',
    ];

    protected $message = [
        'id.require'        => '核心参数错误',
        'sort.require'      => '缺少更新条件',
        'sort.integer'      => '参数必须为整数',
        'sort.egt'          => '参数必须大于或等于0',
        'pid.require'       => '缺少父级菜单参数',
        'pid.integer'       => '父级菜单参数错误',
        'pid.egt'           => '父级菜单参数错误',
        'title.require'     => '菜单名称不能为空',
        'title.unique'      => '此菜单名称已存在',
        'status.in'         => '核心参数错误'
    ];

    protected $scene = [
        'editsort' => ['id', 'sort'],
        'add'      => ['title', 'pid', 'status'],
        'edit'     => ['id','title', 'pid', 'status']
    ];
}
