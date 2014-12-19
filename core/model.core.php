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
 * @NAME model 数据模型基本类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Model
{
	/**
     * @TYPE var
     * @NAME data 数据
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_db = false;

    protected $_name = '';
    
    protected $_project = '';
    
    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function __construct($project = false)
	{
		$this->project($project);
	}
	
	public function project($project = false)
	{
		$this->_project = $project ? $project : siscon::$global['project'];
		$this->_db = siscon::core('store');
		$this->_db->init($this->_project);
		return $this;
	}

	/**
     * @TYPE function
     * @NAME template 模板
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function one($id, $type = 'id', $where = array())
	{
        $where[$type] = $id;
		return $this->_db->load('selectOne', $this->_name, array('where' => $where));
	}
	
	/**
     * @TYPE function
     * @NAME template 模板
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function info($where = array())
	{
		$where['state'] = 1;
		return $this->_db->load('selectOne', $this->_name, array('where' => $where));
	}

	/**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function all($where = array(), $page = false, $order = true, $data = '*')
	{
        if($order == true)
        {
            $where['order^mdate'] = 'desc';
        }
        $where['state'] = 1;
		return $this->_db->load('select', $this->_name, array('where' => $where, 'page' => $page, 'data' => $data));
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function allkey($where = array(), $key = 'id', $array = false, $page = false, $order = true)
	{
        $result = array();
        $data = $this->all($where, $page, $order);
        if($data)
        {
            foreach($data as $k => $v)
            {
                if($v[$key])
                {
                    if($array == true)
                    {
                        $result[$v[$key]][] = $v;
                    }
                    else
                    {
                        $result[$v[$key]] = $v;
                    }
                }
            }
        }

        return $result;
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function exists($where = array())
	{
		return $this->_db->load('selectOne', $this->_name, array('where' => $where));
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function eupdate($exists, $data, $state = false)
	{
        if($state == false)
        {
            $id = siscon::input('id');
        }
        if($exists)
        {
            if($id > 0)
            {
                $exists['id!'] = $id;
            }
            
            $exists = $this->exists($exists);
            if($state == false && $exists)
            {
                siscon::error('不能插入重复的数据');
            }
            elseif($exists)
            {
				$id = $exists['id'];
			}
        }

        

        if($id > 0)
        {
			$data['mdate'] = SIS_TIME;
            $this->update($data, array('id' => $id));
        }
        else
        {
            $id = $this->insert($data);
        }

        return $id;
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function update($data, $where)
	{
		if(siscon::$global['template'] == 'manage')
		{
			$admin = siscon::core('save')->get('admin');
			$data['muser'] = $admin['name'];
		}
		return $this->_db->load('update', $this->_name, array('where' => $where, 'data' => $data));
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function delete($id)
	{
        $where['id'] = $id;
        $data['state'] = 2;
		return $this->_db->load('update', $this->_name, array('where' => $where, 'data' => $data));
	}
	
	/**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function delete_w($where)
	{
		return $this->_db->load('delete', $this->_name, array('where' => $where));
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function insert($data)
	{
		if(siscon::$global['template'] == 'manage')
		{
			$admin = siscon::core('save')->get('admin');
			$data['cuser'] = $data['muser'] = $admin['name'];
		}
		return $this->_db->load('insert', $this->_name, array('data' => $data));
	}

    /**
     * @TYPE function
     * @NAME model 模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function setName($name, $project = false)
	{
		if($this->_project != $project)
		{
			$this->project($project);
		}
		return $this->_name = $name;
	}
}
