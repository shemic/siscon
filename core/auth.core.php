<?php
/*~:SISCON:~
.---------------------------------------------------------------------------.
|  Software: SISCON - 一款用于php敏捷开发的架构程序                         		|
|   Version: 1.0.0                                                          |
|   Contact: 暂无                                                           	|
|      Info: 暂无                                                           	|
|   Support: 暂无                                                           	|
| --------------------------------------------------------------------------|
|    Author: Leo (suwibin.yu)                                               |
| Copyright (c) 2013-2018, Leo. All Rights Reserved.                        |
'---------------------------------------------------------------------------'

/**
 * @TYPE class
 * @NAME model 数据模型基本类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Auth
{ 
	/**
     * @TYPE function
     * @NAME 验证权限
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function init($method)
	{
        $save = siscon::core('save')->init();
        $admin = $save->get('admin');
        if(!$admin)
        {
            siscon::location(siscon::link('admin/manage/login'));die;
        }
        $where['auth'] = $method;
        $info = siscon::model('auth', 'admin')->info($where);
        if(!$info)
        {
            $where['cuser'] = $where['muser'] = $admin['name']; 
            $auth_id = siscon::model('auth', 'admin')->eupdate($where, $where, true);
        }
        $role = siscon::model('role', 'admin')->info(array('id' => $admin['role_id']));
        if($role && $role['auth'])
        {
            if($admin['id'] == 1 || $role['auth'] == 'all')
            {
                return;
            }
            $role['auth'] = explode(',', $role['auth']);
            if(!in_array($method, $role['auth']))
            {
                siscon::error('您没有操作权限');
            }
        }
        else
        {
            siscon::error('您没有操作权限');
        }
	}
}
