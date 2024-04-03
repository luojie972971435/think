<?php

namespace app\systemadmin\controller;

use app\api\controller\Order;
use app\common\model\disk\DiskJoinLog;
use app\common\model\ranking\BonusPools;
use app\common\model\users\Users;
use app\common\model\orders\Orders;
use app\common\model\users\UsersIncomeLog;
use app\common\model\users\UsersIntegrallog;
use think\Db;
use think\facade\Config;
use think\facade\Request;
use app\common\model\admin\Admin;
use app\common\model\users\UsersJoinTask;
use app\common\model\users\SetupLevel;

class Main extends Adminbase
{

    public function index()
    {
        
        return view('index');
    }

    /**
     * 获取LP总数
     */
    // public function getTotalLp()
    // {
    //     $num = 0;
    //     try {
    //         $secret = Config::get('secret');
    //         $token_address = Config::get('token_address');
    //         $kit = new Kit(NodeClient::create('mainNet'), Credential::fromKey($secret));
    //         $balanceOf = $kit->bep20($token_address)->totalSupply();
    //         $num = number_format((string)$balanceOf->value / 1000000000000000000, 18, '.', '');;
    //     } catch (\Exception $e) {
    //         return $num;
    //     }
    //     return $num;
    // }


    public function info()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'Admin.editinfo');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $id = $this->administrator['id'];
                $admin = Admin::getInfo(['id' => $id], 'id,status');
                if (empty($admin) || $admin['status'] != 1) {
                    return $this->port(400, lang('admin_error'));
                }
                $data = [
                    'id' => $admin['id'],
                    'realname' => $param['realname'],
                    'sex' => $param['sex'],
                    'tel' => $param['tel'],
                    'email' => $param['email'],
                    'remarks' => $param['remarks']
                ];
                Db::startTrans();
                try {
                    $update = Admin::updateIdTo($data);
                    if ($update == true) {
                        $token = validateMyshopToken($this->param('validate_form'),$this->param('Myshop_Token'));
                        if ($token['code'] == 200) {
                            Db::commit();
                            return $this->port(200, lang('edit_success'));
                        } else {
                            Db::rollback();
                            return $this->port(400, lang('edit_error'),['token' => $token['token']]);
                        }
                    } else {
                        Db::rollback();
                        return $this->port(400, lang('edit_error'));
                    }
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return $this->port(400, lang('edit_error'));
                }
            }
        }
        $id = $this->administrator['id'];
        $admin = Admin::getInfo(['id' => $id], 'username,tel,sex,email,remarks,realname');
        return view("info", ['admin' => $admin]);
    }

    public function password()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'Admin.password');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $id = $this->administrator['id'];
                $admin = Admin::getInfo(['id' => $id], 'id,salt,pwd,status');
                if (empty($admin) || $admin['status'] != 1) {
                    return $this->port(400, lang('admin_error'));
                }
                if (!vae_get_password($param['pwd'], $admin['salt'], $admin['pwd'])) {
                    return $this->port(400, lang('pwd_error'));
                }
                if ($param['pwd'] == $param['pwd_o']) {
                    return $this->port(400, lang('pwd_o_error'));
                }
                $salt = create_randomstr(13);
                $data = [
                    'id'   => $admin['id'],
                    'salt' => $salt,
                    'pwd'  => vae_set_password($param['pwd_o'], $salt)
                ];
                Db::startTrans();
                try {
                    $update = Admin::updateIdTo($data);
                    if ($update == true) {
                        $token = validateMyshopToken($this->param('validate_form'),$this->param('Myshop_Token'));
                        if ($token['code'] == 200) {
                            Db::commit();
                            return $this->port(200, lang('edit_success'));
                        } else {
                            Db::rollback();
                            return $this->port(400, lang('edit_error'),['token' => $token['token']]);
                        }
                    } else {
                        Db::rollback();
                        return $this->port(400, lang('edit_error'));
                    }
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return $this->port(400, lang('edit_error'));
                }
            }
        }
        return view('password');
    }
}
