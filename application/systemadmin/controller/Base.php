<?php

namespace app\systemadmin\controller;

use think\Controller;
use think\facade\Session;
use think\facade\Config;
use think\facade\Request;
use app\common\model\admin\Admin;
use module\Redis as InRedis;

class Base extends Controller
{
    protected $administrator = [];

    protected $conf;
    protected $redis;
    protected $ip;
    protected $ips;
    protected $time;

    public function initialize()
    {
        parent::initialize();
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers:DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type, Accept-Language, Origin, Accept-Encoding");
        $this->conf  = Config::get('system.');
        $this->redis = new InRedis();
        $this->ip    = get_client_ip();
        $this->ips   = sprintf("%u", ip2long($this->ip));
        $this->time  = time();
        // 检查IP地址访问,黑名单ip
        // if ($this->conf['ALLOW_IP']) {
        //     if (in_array($this->ip, parse_config_attr($this->conf['ALLOW_IP']))) {
        //         return $this->port(403, lang('403'));
        //     }
        // }
    }

    /**
     * 自定义json返回数据
     */
    protected static function port($code = 200, $msg = "OK", $data = [], $url = '', $httpCode = 200, $header = [], $options = [], $open = false)
    {
        $port =  vae_assign($code, $msg, $data, $url, $httpCode, $header, $options, $open);
        return $port;
    }

    /**
     * 自定义参数接收函数
     */
    protected static function param($key = "")
    {
        $param = vae_get_param();
        if (!empty($key) && isset($param[$key])) {
            $param = $param[$key];
        } elseif (!empty($key) && !isset($param[$key])) {
            $param = null;
        } else {
            $param = $param;
        }
        return $param;
    }

    /**
     * 验证单点登录状态，Token信息验证
     */
    protected function _checkLogin()
    {
        if (Session::has("vae_admin_sign")) {
            $req = (array)json_decode(base64_decode(Session::get('vae_admin_sign')));
            if (empty($req) || empty($req['id']) || empty($req['token'])) {
                return false;
            }
            
            $token = html_entity_decode($req['token']);
            $administrator = Admin::getInfos(['admin.id' => $req['id']], 'admin.id,admin.username,admin.pwd,admin.salt,admin.status,admin.last_login_time,admin.last_login_ip,admin.login_num,admin.realname,admin.tel,admin.email,admin.sex,admin.remarks,group.title as groupName,access.group_id');
            if (empty($administrator) || $administrator['status'] != 1) {
                return false;
            }
            $uk    = Session::get('vae_admin_token', 'admin_index');
            $hash_token = md5($uk[0] . md5($uk[1]) . $administrator['username'] . $uk[2]);
            if ((!in_array(Request::controller(), ['login']) && $hash_token !== $token) || time() - Session::get('vae_admin_time') > 7200) {
                return false;
            } else {
                $this->administrator = $administrator;
                Session::set('vae_admin_time', time());
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 清除登录Token
     */
    protected function delLoginSession()
    {
        if (Session::has("vae_admin_time")) {
            Session::delete("vae_admin_time");
        }
        if (Session::has('vae_admin_token', 'admin_index')) {
            Session::delete('vae_admin_token', 'admin_index');
        }
        if (Session::has("vae_admin_sign")) {
            Session::delete("vae_admin_sign");
        }
        return true;
    }
}
