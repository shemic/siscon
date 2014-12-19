<?php
/*~:SISCON:~
.---------------------------------------------------------------------------.
|  Software: SISCON - 一款用于php敏捷开发的架构程序                         |
|   Version: 1.0.0                                                          |
|   Contact: 暂无                                                           |
|      Info: 暂无                                                           |
|   Support: 暂无                                                           |
| ------------------------------------------------------------------------- |
|    Author: Leo (suwibin.yu)                                               |
| Copyright (c) 2013-2018, Leo. All Rights Reserved.                        |
'---------------------------------------------------------------------------'

/**
 * @TYPE class
 * @NAME module 模块基本类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Module
{
	/**
     * @TYPE var
     * @NAME data 数据
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_data = array();

    /**
     * @TYPE var
     * @NAME model 自动载入的数据模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_model = false;
    
    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function __construct()
	{
		$this->init();
        if($this->_model && is_string($this->_model))
        {
            $this->_model = $this->model($this->_model);
        }
        $this->_data['link'] = $_SERVER['HTTP_REFERER'];
	}

	/**
     * @TYPE function
     * @NAME init 子类构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
	public function init()
	{}

	/**
     * @TYPE function
     * @NAME template 模板
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function template($name)
	{
		$data = $this->_data;
		include(SIS_MODULE_ROOT . siscon::$global['project'] . '/' . siscon::$global['template'] . '/' . $name . '.html');
	}

	/**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function model($name, $project = false)
	{
		return siscon::model($name, $project);
	}
}
