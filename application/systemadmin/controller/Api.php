<?php

namespace app\systemadmin\controller;

use think\facade\Config;
use think\facade\Env;
use app\systemadmin\controller\Adminbase;
use app\common\model\admin\AdminRule;
use app\common\model\admin\AdminGroup;
use app\common\model\admin\AdminMenu;

class Api extends Adminbase
{
    // 图片上传
    public function uploadInst()
    {
        $param = $this->param();
        $module = isset($param['module']) ? $param['module'] : 'systemadmin';
        $use = isset($param['use']) ? $param['use'] : 'thumb';
        $res = int_upload($module, $use);
        if ($res['code'] == 1) {
            return $this->port(1, '', $res['data']);
        } else {
            return $this->port(0, $res['msg']);
        }
    }

    // 文件上传
    public function upload()
    {
        $request = new \think\Request();
        if ($request->file('file')) {
            $file = $request->file('file');
        } else {
            return $this->port(0, "请上传文件");
        }
        $savePath = Env::get('ROOT_PATH') . 'public/upload/systemadmin/thumb';
        $conf = Config::get('system.');
        $size = (int)$conf['FILE_SIZE'];
        $info = $file->validate(['size' => 1024 * 1024 * $size, 'ext' => $conf['FILE_EXT']])->rule($conf['FILE_RULE'])->move($savePath);
        if ($info) {
            // 获取保存的文件名
            $vphotograph = '/upload/systemadmin/thumb/' . $info->getSaveName();
            return $this->port(200, '', $vphotograph);;
        } else {
            return $this->port(400, '上传失败：' . $file->getError());
        }
    }




    protected function list_to_tree($list, $group = [], $pk = 'id', $pid = 'pid', $child = 'list', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
                $refer[$data[$pk]]['name'] = $list[$key]['title'];
                $refer[$data[$pk]]['value'] = $list[$key]['id'];
                if (!empty($group) and in_array($list[$key]['id'], $group)) {
                    $refer[$data[$pk]]['checked'] = true;
                }
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                    $tree[$data[$pk]]['name'] = $list[$key]['title'];
                    $tree[$data[$pk]]['value'] = $list[$key]['id'];
                    if (!empty($group) and in_array($list[$key]['id'], $group)) {
                        $tree[$data[$pk]]['checked'] = true;
                    }
                } else {
                    if (!empty($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][$data[$pk]] = &$list[$key];
                        $parent[$child][$data[$pk]]['name'] = $list[$key]['title'];
                        $parent[$child][$data[$pk]]['value'] = $list[$key]['id'];
                        if (!empty($group) and in_array($list[$key]['id'], $group)) {
                            $parent[$child][$data[$pk]]['checked'] = true;
                        }
                    }
                }
            }
        }
        return $tree;
    }

    //获取权限树所需的节点列表
    public function getRuleTree()
    {
        $rule = AdminRule::getList([], 'id,pid,title', 'create_time asc');
        $group = [];
        if (!empty($this->param('id'))) {
            $group_list = AdminGroup::getValue(['id' => $this->param('id')], 'rules');
            if (!empty($group_list)) {
                $group = explode(',', $group_list);
            }
        }
        $list = $this->list_to_tree($rule, $group);
        $data['trees'] = $list;
        return $this->port(200, '', $data);
    }

    //获取菜单树列表
    public function getMenuTree()
    {
        $rule = AdminMenu::getList([], 'id,pid,title', 'create_time asc');
        $group = [];
        if (!empty($this->param('id'))) {
            $group_list = AdminGroup::getValue(['id' => $this->param('id')], 'menus');
            if (!empty($group_list)) {
                $group = explode(',', $group_list);
            }
        }
        $list = $this->list_to_tree($rule, $group);
        $data['trees'] = $list;
        return $this->port(200, '', $data);
    }
}
