<?php

namespace app\systemadmin\controller;

use think\Db;
use think\facade\Config;
use think\facade\Request;
use think\facade\Env;
use app\systemadmin\controller\Adminbase;
use app\common\model\conf\Conf as Confs;

class Conf extends Adminbase
{

	public function index()
	{
		return view('index');
	}

	// 获取配置管理列表
	public function getConfList()
	{
		$param = $this->param();
		$where = [];
		if (!empty($param['title'])) {
			$where[] = ['title', '=', $param['title']];
		}
		if (!empty($param['name'])) {
			$where[] = ['name', '=', $param['name']];
		}
		$page = !empty($param['limit']) ? $param['limit'] : 0;
		$list = Confs::systemPage($where, $page);
		return $this->port(200, '', $list);
	}

	// 修改配置状态
	public function AjaxConfstatus()
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
				$res[] = Confs::updateIdTo($param);
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

	//删除配置
	public function  delConf()
	{
		if (Request::isGet()) {
			Db::startTrans();
			try {
				$id  = $this->param("id");
				$res[] = Confs::deleteIdTo($id);
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

	// 添加保存配置
	public function add()
	{
		if (Request::isPost()) {
			$param = $this->param();
			$result = $this->validate($param, 'Conf.add');
			if ($result !== true) {
				return $this->port(400, $result);
			} else {
				$data = [
					'name'   => $param['name'],
					'title'  => $param['title'],
					'type'   => $param['type'],
					'group'  => $param['group'],
					'value'  => $param['value'],
					'extra'  => $param['extra'],
					'remark' => $param['remark']
				];
				Db::startTrans();
				try {
					$res[] = Confs::insertTo($data);
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
		$conf = Config::get('system.');
		$group = parse_config_attr($conf['CONFIG_GROUP_LIST']);
		$type = parse_config_attr($conf['CONFIG_TYPE_LIST']);
		return view('add', ['group' => $group, 'type' => $type]);
	}

	// 修改保存配置
	public function edit()
	{
		if (Request::isPost()) {
			$param = $this->param();
			$result = $this->validate($param, 'Conf.edit');
			if ($result !== true) {
				return $this->port(400, $result);
			} else {
				$data = [
					'id'     => $param['id'],
					'name'   => $param['name'],
					'title'  => $param['title'],
					'type'   => $param['type'],
					'group'  => $param['group'],
					'value'  => $param['value'],
					'extra'  => $param['extra'],
					'remark' => $param['remark']
				];
				Db::startTrans();
				try {
					$res[] = Confs::updateIdTo($data);
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
		$detail = Confs::getInfo(['id' => $id], 'id,name,title,type,group,value,extra,remark');
		$conf   = Config::get('system.');
		$group  = parse_config_attr($conf['CONFIG_GROUP_LIST']);
		$type   = parse_config_attr($conf['CONFIG_TYPE_LIST']);
		return view('edit', ['group' => $group, 'type' => $type, 'detail' => $detail]);
	}

	// 平台配置
	public function save()
	{
	    $uid = $this->administrator['id'];
		$system = Config::get('system.');
		$id  = empty($this->param("id")) ? 1 : $this->param("id");
		$group = parse_config_attr($system['CONFIG_GROUP_LIST']);
		$list = Confs::getConfList(['status' => 1, 'group' => $id], 'id,name,title,type,group,value,extra,remark', 'sort desc,id asc');
		return view('save', ['group' => $group, 'id' => $id, 'list' => $list,'uid' => $uid]);
	}

	// 平台配置保存
	public function saveBase()
	{
		if (Request::isPost()) {
			$path = Env::get('root_path') . 'config/system.php';
			$config = include $path;
			$param = $this->param();
			$arr = [];
			Db::startTrans();
			try {
				foreach ($param['config'] as $k => $val) {
					$config[$k] = $val;
					$conf = [
						'name'  => $k,
						'value' => $val
					];
					$arr[] = Confs::saveConf($conf);
				}
				$arr[] = $data = "<?php\r\nreturn " . var_export($config, true) . ";\r\n?>";
				if (file_put_contents($path, $data)) {
					if (!in_array(false, $arr)) {
						Db::commit();
						return $this->port(200, lang('edit_success'));
					} else {
						Db::rollback();
						return $this->port(400, lang('edit_error'));
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
}
