<?php

namespace app\systemadmin\controller;

use think\Db;
use think\facade\Cache;
use think\facade\Request;
use app\systemadmin\controller\Adminbase;
use app\common\model\admin\AdminGroup;

class Group extends Adminbase
{
    // 列表
    public function index()
    {
        return view('index');
    }

    // 获取列表数据
    public function getGroupList()
    {
        $param = $this->param();
        $where = array();
        if (!empty($param['title'])) {
            $where[] = ['title', '=', $param['title']];
        }
        $page = !empty($param['limit']) ? $param['limit'] : 0;
        $group = AdminGroup::systemPage($where, $page, 'id,title,info,status,create_time');
        return $this->port(200, '', $group);
    }

    // 添加保存管理组
    public function add()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'Group.add');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $data['title']  = $param['title'];
                $data['status'] = $param['status'];
                $data['info']   = $param['info'];
                if (!empty($param['rules'])) {
                    $data['rules'] = implode(',', $param['rules']);
                }
                if (!empty($param['menus'])) {
                    $data['menus'] = implode(',', $param['menus']);
                }
                Db::startTrans();
                try {
                    $res[] = AdminGroup::insertTo($data);
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
        return view('add');
    }

    // 修改保存管理组
    public function edit()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'Group.edit');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                if ($param['id'] == 1) {
                    return $this->port(400, lang('auth_error'));
                }
                $data['id'] = $param['id'];
                $data['title'] = $param['title'];
                $data['info'] = $param['info'];
                $data['status'] = $param['status'];
                if (!empty($param['rules'])) {
                    $data['rules'] = implode(',', $param['rules']);
                }
                if (!empty($param['menus'])) {
                    $data['menus'] = implode(',', $param['menus']);
                }
                Db::startTrans();
                try {
                    $res[] = AdminGroup::updateIdTo($data);
                    $res[] = Cache::clear('VAE_ADMIN_MENU');
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
        $group = AdminGroup::getInfo(['id' => $id],'id,title,status,info,rules,menus');
        $group['rules'] = explode(',', $group['rules']);
        $group['menus'] = explode(',', $group['menus']);
        return view('edit', ['group' => $group]);
    }

    // 删除管理组
    public function delGroup()
    {
        $id    = $this->param("id");
        if ($id == 1) {
            return $this->port(400, lang('auth_error'));
        }
        if (AdminGroup::deleteIdTo($id) !== false) {
            return $this->port(200, lang('delete_success'));
        } else {
            return $this->port(400, lang('delete_error'));
        }
    }
}
