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
 * @NAME manage 基本管理类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class User_Manage extends Module
{
    /**
     * @TYPE var
     * @NAME model 自动载入的数据模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_model = 'user';

    /**
     * @TYPE function
     * @NAME list 用户列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function list_get_ajax()
	{
        $get = $_GET;
        $where = array();
        $page['template'] = 'list';
        $search = array();
        if($get)
        {
			foreach($get as $k => $v)
			{
				if(strstr($k, 'search_'))
				{
					$k = str_replace('search_','',$k);
					$search[$k] = $v;
				}
			}
		}
		$search['order'] = $search['order'] ? $search['order'] : 1;
		$this->_data['order'] = array
        (
			1 => array
			(
				'name' => '按照注册时间正序',
				'value' => array('cdate', 'asc'),
			),
			2 => array
			(
				'name' => '按照注册时间倒序',
				'value' => array('cdate', 'desc'),
			),
        );
		$this->_data['level'] = array(1 => '普通用户',100 => '管理员');
		$this->_data['status'] = array(1 => '正常',2 => '冻结');
        $order = true;
        if($search)
        {
			if($search['status'] > 0)
			{
				$where['status'] = $search['status'];
			}
			if($search['level'] > 0)
			{
				$where['level'] = $search['level'];
			}
			if($search['sctime'] > 0)
			{
				$where['cdate^>='] = siscon::maketime($search['sctime']);
			}
			if($search['ectime'] > 0)
			{
				$where['cdate^<='] = siscon::maketime($search['ectime']);
			}
			if($search['name'])
			{
				$where['username^like^and^('] = $search['name'];
				$where['email^like^or^)'] = $search['name'];
			}
			if($search['order'] > 0)
			{
				$where['order^' . $this->_data['order'][$search['order']]['value'][0]] = $this->_data['order'][$search['order']]['value'][1];
                $order = false;
			}
			$this->_data['search'] = $search;
            //print_r($where);
        }

        $this->_data['search'] = $search;
		$this->_data['list'] = $this->_model->all($where, $page, $order);

        
		$this->template('list');
	}
    /**
     * @TYPE function
     * @NAME list 订阅列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function ding_list_get_ajax()
	{
        $get = $_GET;
        $where = array();
        $page['template'] = 'list';
        $search = array();
        if($get)
        {
			foreach($get as $k => $v)
			{
				if(strstr($k, 'search_'))
				{
					$k = str_replace('search_','',$k);
					$search[$k] = $v;
				}
			}
		}
		$this->_data['order'] = array
        (
			1 => array
			(
				'name' => '按照订阅时间正序',
				'value' => array('cdate', 'asc'),
			),
			2 => array
			(
				'name' => '按照订阅时间倒序',
				'value' => array('cdate', 'desc'),
			),
        );
		//$this->_data['status'] = array(1 => '正常',2 => '冻结');
        $order = true;
        if($search)
        {
			if($search['status'] > 0)
			{
				$where['status'] = $search['status'];
			}
			if($search['sctime'] > 0)
			{
				$where['cdate^>='] = siscon::maketime($search['sctime']);
			}
			if($search['ectime'] > 0)
			{
				$where['cdate^<='] = siscon::maketime($search['ectime']);
			}
			if($search['name'])
			{
				$where['email^like'] = $search['name'];
			}
			if($search['order'] > 0)
			{
				$where['order^' . $this->_data['order'][$search['order']]['value'][0]] = $this->_data['order'][$search['order']]['value'][1];
                $order = false;
			}
			$this->_data['search'] = $search;
            //print_r($where);
        }

        $this->_data['search'] = $search;
		$this->_data['list'] = siscon::model('user_ding')->all($where, $page, $order);

        
		$this->template('ding_list');
	}

    /**
     * @TYPE function
     * @NAME add 新增/更新管理员
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = $this->_model->one($id);
        }

        # 获取部门
        $this->_data['group'] = siscon::model('group')->all();
        # 获取用户组
        $this->_data['role'] = siscon::model('role')->all();
		$this->template('update');
	}

    /**
     * @TYPE function
     * @NAME add 新增/更新管理员
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function edit_get_ajax()
	{
        $admin = siscon::core('save')->init()->get('admin');
        $id = $admin['id'];
        if($id > 0)
        {
		    $this->_data['info'] = $this->_model->one($id);
        }

        # 获取部门
        $this->_data['group'] = siscon::model('group')->all();
        # 获取用户组
        $this->_data['role'] = siscon::model('role')->all();
		$this->template('edit');
	}

    /**
     * @TYPE function
     * @NAME add 新增管理员
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('管理员账号不能为空');
        }
        $exists = $data;

		siscon::input('password') && $data['password'] = md5(siscon::input('password'));

        $data['email'] = siscon::input('email');
        $data['mobile'] = siscon::input('mobile');
        $data['group_id'] = siscon::input('group_id');
        $data['role_id'] = siscon::input('role_id');
        $data['pic'] = siscon::input('pic');
        $data['weibo'] = siscon::input('weibo');
        $data['info'] = siscon::input('info');
        $data['truename'] = siscon::input('truename');
        $data['status'] = siscon::input('status', 1);

        $this->_model->eupdate($exists, $data);

        siscon::out('操作成功');
	}

    /**
     * @TYPE function
     * @NAME add 删除管理员
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function delete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
            $where['id'] = $id;
            $data['status'] = 2;
		    $this->_model->update($data, $where);
        }
	}
    public function undelete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
            $where['id'] = $id;
            $data['status'] = 1;
		    $this->_model->update($data, $where);
        }
	}
    public function up_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
            $where['id'] = $id;
            $data['level'] = 100;
		    $state = siscon::model('user')->update($data, $where);
            if($id)
            {
                $one = siscon::model('user')->one($id);
                $update['uid'] = $id;
                $exists = $update;
                $update['name'] = $one['username'];
                $update['password'] = $one['password'];
                $update['pic'] = $one['pic'];
                $update['email'] = $one['email'];
                $update['status'] = 1;
                $update['state'] = 1;
                $update['role_id'] = 2;
                $update['group_id'] = 1;
                siscon::model('admin', 'admin')->eupdate($exists, $update, true);
            }
        }
	}
    public function down_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
            $where['id'] = $id;
            $data['level'] = 1;
		    siscon::model('user')->update($data, $where);
            $w['uid'] = $id;
            siscon::model('admin', 'admin')->delete($w);
        }
	}

}
