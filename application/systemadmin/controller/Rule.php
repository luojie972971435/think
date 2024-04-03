<?php

namespace app\systemadmin\controller;

use think\Db;
use think\facade\Request;
use app\systemadmin\controller\Adminbase;
use app\common\model\admin\AdminRule;
use app\common\model\admin\AdminGroup;

class Rule extends Adminbase
{
    // 节点管理
    public function index()
    {
        return view("index");
    }

    // 获取节点管理列表
    public function getRuleList()
    {
        $where = [];
        $rule = AdminRule::getList($where, 'id,pid,title,name,condition,status');
        return $this->port(200, '', $rule);
    }

    // 编辑节点状态
    public function ajaxRulestatus()
    {
        if (Request::isGet()) {
            $id = $this->param('id');
            $checked = $this->param('checked');
            $checked = $checked == 'true' ? 1 : 0;
            $param = [
                'id' => $id,
                'status' => $checked
            ];
            Db::startTrans();
            try {
                $res[] = AdminRule::updateIdTo($param);
                if (!in_array(false, $res)) {
                    Db::commit();
                    return $this->port(200, lang('edit_success'));
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

    //删除
    public function deleteRule()
    {
        if (Request::isGet()) {
            $id    = $this->param("id");
            $detail = AdminRule::getValue(["pid" => $id], 'id');
            if (!empty($detail)) {
                return $this->port(400, lang('delete_level_error'));
            }
            Db::startTrans();
            try {
                $res[] = AdminRule::deleteIdTo($id);
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

    // 新增节点页面
    public function add()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'AdminRule.add');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $data = [
                    'title'  => $param['title'],
                    'pid'    => $param['pid'],
                    'name'    => $param['name'],
                    'condition'  => $param['condition'],
                    'status' => $param['status']
                ];
                $rules = AdminGroup::getValue(['id' => 1], 'rules');
                Db::startTrans();
                try {
                    $res[] = $idrule = AdminRule::insertGetIdTo($data);
                    if (empty($rules)) {
                        $rules_li = $idrule;
                    } else {
                        $rules_li = $rules . ',' . $idrule;
                    }
                    $res[] = AdminGroup::updateIdTo(['id' => 1, 'rules' => $rules_li]);
                    if (!in_array(false,$res)) {
                        $token = validateMyshopToken($this->param('validate_form'),$this->param('Myshop_Token'));
                        if ($token['code'] == 200) {
                            Db::commit();
                            return $this->port(200, lang('add_success'));
                        } else {
                            Db::rollback();
                            return $this->port(400, lang('add_error'),['token' => $token['token']]);
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
        $rule_list = AdminRule::getList(['status' => 1], 'id,pid,title', 'create_time asc');
        $rulelist  = vae_set_recursion($rule_list);
        return view('add', ['pid' => $this->param("pid"), 'rulelist' => $rulelist]);
    }

    // 编辑节点页面
    public function edit(){
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'AdminRule.edit');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $data = [
                    'id'     => $param['id'],
                    'title'  => $param['title'],
                    'pid'    => $param['pid'],
                    'name'    => $param['name'],
                    'condition'  => $param['condition'],
                    'status' => $param['status']
                ];
                Db::startTrans();
                try {
                    $res[] = AdminRule::updateIdTo($data);
                    if (!in_array(false,$res)) {
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
        $id = $this->param("id");
        $detail = AdminRule::getInfo(['id' => $id],'id,pid,title,name,condition,status');
        $rule_list = AdminRule::getList(['status' => 1], 'id,pid,title', 'create_time asc');
        $rulelist  = vae_set_recursion($rule_list);
        return view('edit', ['detail' => $detail, 'rulelist' => $rulelist]);
    }  
}
