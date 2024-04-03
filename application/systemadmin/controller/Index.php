<?php

namespace app\systemadmin\controller;

use app\systemadmin\controller\Adminbase;

class Index extends Adminbase
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$treeMenu = $this->treeMenu();
		return view('index', ['treeMenu' => $treeMenu, 'admin' => $this->administrator]);
	}
}
