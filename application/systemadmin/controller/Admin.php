<?php

namespace app\systemadmin\controller;

use think\DB;
use think\facade\Cache;
use think\facade\Request;
use app\systemadmin\controller\Adminbase;
use app\common\model\admin\Admin as Admins;
use app\common\model\admin\AdminGroupAccess;
use app\common\model\admin\AdminGroup;

class Admin extends Adminbase
{

    // 平台账号列表
    public function index()
    {
        $group = AdminGroup::getList(['status' => 1], 'id,title');
        return view('index', ['group' => $group]);
    }

    // 获取平台账号列表
    public function getAdminList()
    {
        $param = $this->param();
        $where = [];
        if (!empty($param['username'])) {
            $where[] = ['admin.username', '=', $param['username']];
        }
        if (!empty($param['group_id'])) {
            $where[] = ['access.group_id', '=', $param['group_id']];
        }
        $page = !empty($param['limit']) ? $param['limit'] : 0;
        $admin = Admins::systemPage($where, $page);
        return $this->port(200, '', $admin);
    }

    // 删除平台账号
    public function deleteAdmin()
    {
        if (Request::isGet()) {
            $id    = $this->param("id");
            $uid = $this->administrator['id'];
            if ($uid != 1 || $id == 1) {
                return $this->port(400, lang('auth_error'));
            }
            Db::startTrans();
            try {
                $res[] = Admins::deleteIdTo($id);
                $res[] = AdminGroupAccess::deleteTo(['uid' => $id]);
                if (!in_array(false, $res)) {
                    Db::commit();
                    return $this->port(200, lang('delete_success'));
                } else {
                    Db::rollback();
                    return $this->port(400, lang('delete_error'));
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return $this->port(400, lang('delete_error'));
            }
        }
    }

    // 添加平台账号
    public function add()
    {
        if (Request::isPost()) {
            $uid = $this->administrator['id'];
            if ($uid != 1) {
                return $this->port(400, lang('auth_error'));
            }
            $param = $this->param();
            $result = $this->validate($param, 'Admin.add');
            if ($result !== true) {
                return $this->port(0, $result);
            } else {
                $group_id = AdminGroup::getValue(['id' => $param['group_id']]);
                if (empty($group_id)) {
                    return $this->port(400, lang('group_error'));
                }
                $data = [
                    'username' => $param['usernames'],
                    'pwd' => $param['pwd'],
                    'realname' => $param['realname'],
                    'sex' => $param['sex'],
                    'tel' => $param['tel'],
                    'email' => $param['email'],
                    'status' => $param['status'],
                    'remarks' => $param['remarks']
                ];
                Db::startTrans();
                try {
                    $res[] = $id = Admins::insertGetIdTo($data);
                    $groupId = AdminGroupAccess::getInfo(['uid' => $id], 'id,group_id');
                    if (empty($groupId)) {
                        $res[] = AdminGroupAccess::insertTo(['uid' => $id, 'group_id' => $group_id]);
                    } else {
                        if ($groupId['group_id'] != $group_id) {
                            $res[] = AdminGroupAccess::updateIdTo(['id' => $groupId['id'], 'group_id' => $group_id]);
                        }
                    }
                    if (!in_array(false, $res)) {
                        $token = validateMyshopToken($this->param('validate_form'), $this->param('Myshop_Token'));
                        if ($token['code'] == 200) {
                            Db::commit();
                            return $this->port(200, lang('add_success'));
                        } else {
                            Db::rollback();
                            return $this->port(400, lang('add_error'), ['token' => $token['token']]);
                        }
                    } else {
                        Db::rollback();
                        return $this->port(400, lang('add_error'));
                    }
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                    return $this->port(400, lang('add_error'));
                }
            }
        }
        $group = AdminGroup::getList(['status' => 1], 'id,title');
        return view('add', ['group' => $group]);
    }

    // 编辑平台账号
    public function edit()
    {
        if (Request::isPost()) {
            $uid = $this->administrator['id'];
            if ($uid != 1) {
                return $this->port(400, lang('auth_error'));
            }
            $param = $this->param();
            $result = $this->validate($param, 'Admin.edit');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                if ($param['id'] == 1 && $param['group_id'] != 1) {
                    return $this->port(400, lang('auth_error'));
                }
                $group_id = AdminGroup::getValue(['id' => $param['group_id']]);
                if (empty($group_id)) {
                    return $this->port(400, lang('group_error'));
                }
                $salt = create_randomstr(13);
                $data = [
                    'id'       => $param['id'],
                    'username' => $param['usernames'],
                    'salt'     => $salt,
                    'pwd'      => vae_set_password($param['pwd'], $salt),
                    'realname' => $param['realname'],
                    'sex'      => $param['sex'],
                    'tel'      => $param['tel'],
                    'email'    => $param['email'],
                    'status'   => $param['status'],
                    'remarks'  => $param['remarks']
                ];
                if (empty($param['pwd'])) {
                    unset($data['pwd']);
                    unset($data['salt']);
                }
                Db::startTrans();
                try {
                    $res[] = Admins::updateIdTo($data);
                    $res[] = Cache::clear('VAE_ADMIN_MENU');
                    $groupId = AdminGroupAccess::getInfo(['uid' => $param['id']], 'id,group_id');
                    if (empty($groupId)) {
                        $res[] = AdminGroupAccess::insertTo(['uid' => $param['id'], 'group_id' => $group_id]);
                    } else {
                        if ($groupId['group_id'] != $group_id) {
                            $res[] = AdminGroupAccess::updateIdTo(['id' => $groupId['id'], 'group_id' => $group_id]);
                        }
                    }
                    if (!in_array(false, $res)) {
                        $token = validateMyshopToken($this->param('validate_form'), $this->param('Myshop_Token'));
                        if ($token['code'] == 200) {
                            Db::commit();
                            return $this->port(200, lang('edit_success'));
                        } else {
                            Db::rollback();
                            return $this->port(400, lang('edit_error'), ['token' => $token['token']]);
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
        $id = $this->param('id');
        $detail = Admins::getInfoGroupId(['admin.id' => $id], 'admin.id,admin.username,admin.realname,admin.tel,admin.sex,admin.email,admin.remarks,admin.status,access.group_id');
        $group = AdminGroup::getList(['status' => 1], 'id,title');
        return view('edit', ['detail' => $detail, 'group' => $group]);
    }
}
