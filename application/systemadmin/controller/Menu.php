<?php

namespace app\systemadmin\controller;

use think\Db;
use think\facade\Cache;
use think\facade\Request;
use app\systemadmin\controller\Adminbase;
use app\common\model\admin\AdminMenu;
use app\common\model\admin\AdminGroup;

class Menu extends Adminbase
{
    // 菜单管理
    public function index()
    {
        return view("index");
    }

    // 获取菜单管理列表
    public function getMenuList()
    {
        $where = [];
        $menu = AdminMenu::getList($where, 'id,pid,title,src,param,sort,status');
        return $this->port(200, '', $menu);
    }

    // 编辑菜单状态
    public function ajaxMenustatus()
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
                $res[] = AdminMenu::updateIdTo($param);
                $res[] = Cache::clear('VAE_ADMIN_MENU');
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

    // 编辑菜单排序
    public function editSort()
    {
        if (Request::isGet()) {
            $param = $this->param();
            $result = $this->validate($param, 'AdminMenu.editsort');
            if ($result !== true) {
                return $this->port(0, $result);
            } else {
                $data = [
                    'id'   => $param['id'],
                    'sort' => $param['sort']
                ];
                Db::startTrans();
                try {
                    $res[] = AdminMenu::updateIdTo($data);
                    $res[] = Cache::clear('VAE_ADMIN_MENU');
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
    }

    //删除
    public function deleteMenu()
    {
        if (Request::isGet()) {
            $id    = $this->param("id");
            $detail = AdminMenu::getValue(["pid" => $id], 'id');
            if (!empty($detail)) {
                return $this->port(400, lang('delete_level_error'));
            }
            Db::startTrans();
            try {
                $res[] = AdminMenu::deleteIdTo($id);
                $res[] = Cache::clear('VAE_ADMIN_MENU');
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

    // 新增菜单页面
    public function add()
    {
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'AdminMenu.add');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $data = [
                    'title'  => $param['title'],
                    'pid'    => $param['pid'],
                    'src'    => $param['src'],
                    'param'  => $param['param'],
                    'icon'   => $param['icon'],
                    'status' => $param['status']
                ];
                $menus = AdminGroup::getValue(['id' => 1], 'menus');
                Db::startTrans();
                try {
                    $res[] = $idmenu = AdminMenu::insertGetIdTo($data);
                    if (empty($menus)) {
                        $menus_li = $idmenu;
                    } else {
                        $menus_li = $menus . ',' . $idmenu;
                    }
                    $res[] = AdminGroup::updateIdTo(['id' => 1, 'menus' => $menus_li]);
                    $res[] = Cache::clear('VAE_ADMIN_MENU');
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
        $menu_list = AdminMenu::getList(['status' => 1], 'id,pid,title', 'sort asc');
        $menulist  = vae_set_recursion($menu_list);
        return view('add', ['pid' => $this->param("pid"), 'menulist' => $menulist]);
    }

    // 编辑菜单页面
    public function edit(){
        if (Request::isPost()) {
            $param = $this->param();
            $result = $this->validate($param, 'AdminMenu.edit');
            if ($result !== true) {
                return $this->port(400, $result);
            } else {
                $data = [
                    'id'     => $param['id'],
                    'title'  => $param['title'],
                    'pid'    => $param['pid'],
                    'src'    => $param['src'],
                    'param'  => $param['param'],
                    'icon'   => $param['icon'],
                    'status' => $param['status']
                ];
                Db::startTrans();
                try {
                    $res[] = AdminMenu::updateIdTo($data);
                    $res[] = Cache::clear('VAE_ADMIN_MENU');
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
        $detail = AdminMenu::getInfo(['id' => $id],'id,pid,title,src,param,icon,status');
        $menu_list = AdminMenu::getList(['status' => 1], 'id,pid,title', 'sort asc');
        $menulist  = vae_set_recursion($menu_list);
        return view('edit', ['detail' => $detail, 'menulist' => $menulist]);
    }  
}
