<?php

namespace app\systemadmin\controller;

use expand\Auth;
use think\facade\Request;
use think\facade\Cache;
use think\facade\Env;
use app\systemadmin\controller\Base;
use app\common\model\admin\AdminMenu;
use app\common\model\admin\AdminGroup;
use app\common\model\admin\Admin;

class Adminbase extends Base
{
	
	public function __construct()
	{
		parent::__construct();
		if ($this->_checkLogin()) {
			if ($this->administrator['status'] != 1) {
				$this->delLoginSession();
				$this->redirect('/systemadmin/login/index');
			}
            $this->isAccess();
		} else {
			$this->delLoginSession();
			$this->redirect('/systemadmin/login/index');
		}
	}


	protected function treeMenu()
	{
		if (!isset($this->administrator['id']) || $this->administrator['id'] <= 0) {
			return false;
		}
		$uid = $this->administrator['id'];
		$treeMenu = Cache::tag('VAE_ADMIN_MENU')->get('DB_TREE_MENU_' . $uid);
		if (!$treeMenu) {
			$adminMenu = AdminGroup::getJoinValue(['access.uid' => $uid], 'group.menus');
			$map = [
				['id', 'IN', $adminMenu],
				['status', '=', 1]
			];
			$menu =  AdminMenu::getList($map, 'id,pid,title,src,param,icon,sort');
			$treeMenu = vae_list_to_tree($menu);
			Cache::tag('VAE_ADMIN_MENU')->set('DB_TREE_MENU_' . $uid, $treeMenu);
		}
		return $treeMenu;
	}


	// 检测方法是否有权限
	protected function isAccess()
	{
		$params = [
			'controller' => strtolower(Request::controller()),
			'action'     => strtolower(Request::action()),
			'admin_id'   => $this->administrator['id']
		];
		if (($params['controller'] == 'index' && $params['action'] == 'index')) {
			return false;
		}
		if (in_array($params['controller'], ['adminbase', 'main'])) {
			return false;
		}
		$auth = new Auth();
		if (false == $auth->check($params['controller'] . '/' . $params['action'], $params['admin_id'])) {
			return $this->port(400, lang('auth_error'), [], '', 200, [], [], true);
		}
	}

	// 注销登陆
	public function logout()
	{
		$uid = $this->administrator;
		if (!empty($uid)) {
			Cache::tag('VAE_ADMIN_MENU')->rm('DB_TREE_MENU_' . $uid['id']);
			$this->delLoginSession();
			return $this->port(200, lang('logout'));
		}
	}

	// 清楚缓存
	public function clearcache()
	{
		$uid = $this->administrator['id'];
		$TEMP_PATH = Env::get('RUNTIME_PATH') . 'temp/';
		if (delete_dir_file($TEMP_PATH) && Cache::tag('VAE_ADMIN_MENU')->rm('DB_TREE_MENU_' . $uid)) {
			return $this->port(200, lang('clear_success'));
		} else {
			return $this->port(400, lang('clear_error'));
		}
	}
}
