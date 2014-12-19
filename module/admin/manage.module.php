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

class Admin_Manage extends Module
{
    /**
     * @TYPE var
     * @NAME model 自动载入的数据模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_model = 'admin';

    public function login_get()
    {
        $this->_data['refer'] = siscon::link(str_replace('.', '/', siscon::input('refer', '')));
        if(!siscon::input('refer'))
        {
            $this->_data['refer'] = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->_data['refer'];
        }
        $this->_data['user'] = $_SESSION['user'];
        $this->template('login');
    }
    public function login_get_ajax()
    {
        echo '<script>location.reload()</script>';die;
    }
    public function login_out_get()
    {
        $this->_data['refer'] = siscon::link(str_replace('.', '/', siscon::input('refer', '')));
        if(!siscon::input('refer'))
        {
            $this->_data['refer'] = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : $this->_data['refer'];
        }
        siscon::core('save')->init()->un('admin');
        siscon::location($this->_data['refer']);
    }
    public function login_post()
	{
        $where['email'] = siscon::input('username');
        if(!$where['email'])
        {
		    siscon::error('管理员账号不能为空');
        }
        $where['password'] = siscon::input('password');
        if(!$where['password'])
        {
		    siscon::error('管理员密码不能为空');
        }
        $where['password'] = md5($where['password']);
        $where['status'] = 1;
        $info = $this->_model->info($where);
        if(!$info)
        {
            //$all = $this->_model->info();
            //if(!$all)
            //{
                //# 创始人
                //$group['name'] = '默认职位';
                //$role['name'] = '系统管理员';
                //$role['auth'] = 'all';
                //$exists = $where;
                //$group_exists = $group;
                //$role_exists = $role;
                //$where['cuser'] = $where['muser'] = $role['muser'] = $role['cuser'] = $group['muser'] = $group['cuser'] = $where['name'];
                //$where['group_id'] = siscon::model('group')->eupdate($group_exists, $group, true);
                //$group['name'] = '专栏作家';
                //$group_exists = $group;
                //siscon::model('group')->eupdate($group_exists, $group, true);
                //$where['role_id'] = siscon::model('role')->eupdate($role_exists, $role, true);

                //$id = siscon::model('admin')->eupdate($exists, $where, true);
                //$info = $this->_model->info(array('id' => $id));

            //}
        }
        if($info)
        {
            $save = siscon::core('save')->init();
		    $role = siscon::model('role')->one($info['role_id']);
            if($role['menu'])
            {
                $role['menu'] = unserialize(base64_decode($role['menu']));
                $info['menu'] = $role['menu'];
            }
            $info['role_config'] = $role;
            $save->add('admin', $info);
        }
        else
        {
            siscon::error('登录失败');
        }

        siscon::out('1');
	}
    /**
     * @TYPE function
     * @NAME list 管理员列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function list_get_ajax()
	{
        $search = siscon::input('search');
        $where = array();
        $page['template'] = 'list';
        if($search)
        {
            $where['name^like'] = $search;
            $where['info^like^or'] = $search;
        }

        $this->_data['search'] = $search;
		$this->_data['list'] = $this->_model->all($where, $page);

        if($this->_data['list']['data'])
        {
            $data = siscon::str($this->_data['list']['data'], 'group_id,role_id');
            $w['id^in'] = $data['group_id'];
            $this->_data['group'] = siscon::model('group')->allkey($w, 'id');

            $w['id^in'] = $data['role_id'];
            $this->_data['role'] = siscon::model('role')->allkey($w, 'id');
        }
        
		$this->template('list');
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
        # 获取角色
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
        # 获取角色
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
        $data['email'] = siscon::input('email');
        if(!$data['name'])
        {
		    //siscon::error('管理员账号不能为空');
        }
        $exists = $data;

		siscon::input('password') && $data['password'] = md5(siscon::input('password'));

        $data['mobile'] = siscon::input('mobile');
        $data['group_id'] = siscon::input('group_id');
        $data['zhiwei'] = siscon::input('zhiwei');
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
		    $this->_model->delete($id);
        }
	}

	/**
     * @TYPE function
     * @NAME list 权限列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function auth_list_get_ajax()
	{
        $where = array();
        $page['template'] = 'manage/turn/list';
		$this->_data['list'] = siscon::model('auth')->all($where, $page);
        
		$this->template('auth_list');
	}

	/**
     * @TYPE function
     * @NAME add 新增/更新权限
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function auth_update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = siscon::model('auth')->one($id);
        }
        else
        {
            die;
        }
		$this->template('auth_update');
	}

    /**
     * @TYPE function
     * @NAME add 删除权限
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function auth_delete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    siscon::model('auth')->delete($id);
        }
	}

	/**
     * @TYPE function
     * @NAME add 新增角色
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function role_update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('角色名称不能为空');
        }
		//$data['auth'] = implode(',', siscon::input('auth'));
        
		$data['auth'] = 'all';
        $menu = siscon::input('menu');
        if(!$menu)
        {

		    siscon::error('请选择权限');
        }
        $this->_data['menu'] = siscon::$global['define']['menu'];
        foreach($menu as $k => $v)
        {
            $child = siscon::input('menu_'.$v);
            $menu_value[$v] = $this->_data['menu'][$v];
            unset($menu_value[$v]['child']);
            foreach($child as $i => $j)
            {
                if($this->_data['menu'][$v]['child'][$j])
                {
                    $menu_value[$v]['child'][$j] = $this->_data['menu'][$v]['child'][$j];
                }
            }
        }
        $data['menu'] = base64_encode(serialize($menu_value));


        siscon::model('role')->eupdate($data, $data);

        siscon::out('操作成功');
	}
	/**
     * @TYPE function
     * @NAME list 角色列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function role_list_get_ajax()
	{
        $where = array();
        $page['template'] = 'manage/turn/list';
		$this->_data['list'] = siscon::model('role')->all($where, $page);
        
		$this->template('role_list');
	}

	/**
     * @TYPE function
     * @NAME add 新增/更新角色
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function role_update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = siscon::model('role')->one($id);
//            $this->_data['info']['auth'] = explode(',', $this->_data['info']['auth']);
            if($this->_data['info']['menu'])
            {
                $this->_data['my'] = unserialize(base64_decode($this->_data['info']['menu']));
            }
        }
        $this->_data['auth'] = siscon::model('auth')->all();
        $this->_data['menu'] = siscon::$global['define']['menu'];
        $this->_data['cate'] = siscon::model('cate', 'article')->all();
		$this->template('role_update');
	}

    /**
     * @TYPE function
     * @NAME add 删除角色
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function role_delete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    siscon::model('role')->delete($id);
        }
	}

	/**
     * @TYPE function
     * @NAME add 修改权限
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function auth_update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('权限名称不能为空');
        }

        siscon::model('auth')->eupdate($data, $data);

        siscon::out('操作成功');
	}

	/**
     * @TYPE function
     * @NAME list 部门列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function group_list_get_ajax()
	{
        $where = array();
        $page['template'] = 'manage/turn/list';
		$this->_data['list'] = siscon::model('group')->all($where, $page);
        
		$this->template('group_list');
	}

	/**
     * @TYPE function
     * @NAME add 新增/更新部门
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function group_update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = siscon::model('group')->one($id);
        }
		$this->template('group_update');
	}

    /**
     * @TYPE function
     * @NAME add 删除部门
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function group_delete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    siscon::model('group')->delete($id);
        }
	}

	/**
     * @TYPE function
     * @NAME add 新增部门
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function group_update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('部门名称不能为空');
        }
		$data['connect'] = siscon::input('connect');
        if(!$data['connect'])
        {
		    siscon::error('部门联系方式不能为空');
        }

        siscon::model('group')->eupdate($data, $data);

        siscon::out('操作成功');
	}
}
