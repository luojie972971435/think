<?php

namespace app\systemadmin\controller;

use think\facade\Session;
use think\facade\Config;
use think\Exception;
use think\facade\Request;
use app\common\model\admin\Admin;
use app\systemadmin\controller\Base;

class Login extends Base
{
    /**
     * 登录页
     */
    public function index()
    {
        if ($this->_checkLogin()) {
            $this->redirect('/systemadmin/index/index');
        } else {
            $this->delLoginSession();
            return view('index');
        }
    }

    /**
     * 登录接口
     */
    public function toLogin()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'Admin.login');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $administrator = Admin::getInfo(['username' => $param['username']], 'id,username,pwd,salt,status,login_num');
                if (!$administrator || empty($administrator)) {
                    return $this->port(400, lang('user_error'));
                }
                // if (vae_get_password($param['pwd'], $administrator['salt'], $administrator['pwd']) == false) {
                //     return $this->port(400, lang('user_error'));
                // }
                if ($administrator['status'] == 0) {
                    return $this->port(400, lang('user_status'));
                }
                $token = $this->uniqidReal();
                $time  = time();
                $hash_token = md5($token . md5($time) . $administrator['username'] . $this->ip);
                $data = array(
                    'id'              => $administrator['id'],
                    'last_login_ip'   => $this->ips,
                    'last_login_time' => $time,
                    'login_num'       => $administrator['login_num'] + 1
                );
                $res = Admin::updateIdTo($data);
                if ($res == true) {
                    Session::set('vae_admin_token', [$token, $time, $this->ip], 'admin_index');
                    Session::set('vae_admin_time', $time);
                    Session::set('vae_admin_sign', base64_encode(json_encode(['id' => $administrator['id'], 'token' => $hash_token])));
                    return $this->port(200, lang('login_success'));
                } else {
                    return $this->port(400, lang('login_error'));
                }
            }
        }
    }

    /**
     * 登录Token接口
     */
    public function loginToken()
    {
        if (Request::isPost()) {
            $captcha = authcode($this->ips);  // 生成32位字符串
            $logintoken = creatToken($captcha);
            if (empty($logintoken) || empty($captcha)) {
                return $this->port(400, lang('captcha_error'));
            } else {
                return $this->port(200, lang('logintoken'), ['logintoken' => $logintoken]);
            }
        }
    }

    /**
     * 生成唯一的uuid值
     * @param  integer $lenght 生成的uuid长度
     * @return
     */
    protected function uniqidReal($lenght = 13)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
}
