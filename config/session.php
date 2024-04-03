<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------
return [
    // 默认驱动
    'type'       => 'redis',
    // session_name
    'name'       => 'PHPSESSID',
    // session_id的提交变量
    'var_session_id' => '',
    // SESSION_ID的提交变量类型，空为自动检测，支持header
    'id'         => '',
    // SESSION 前缀
    'prefix'     => 'think',
    // session保存时间
    'expire'     => 3600,
    // session 保存路径
    'path'       => '../runtime/session/',
    // session有效期
    'domain'     => '',
    // session 驱动配置
    'use_trans_sid' => 1,
    //是否自动开启 SESSION
    'auto_start' => true,


    // redis 相关配置
    'host'       => '127.0.0.1',
    'port'       => 6379,
    'password'   => '',
    'select'     => 0,
    'timeout'    => 0,
    'persistent' => false,
    'prefix'     => '',
];