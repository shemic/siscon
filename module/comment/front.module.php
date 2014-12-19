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
 * @NAME interface 接口类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Comment_Front extends Module
{
	public function update_get()
	{
        $this->_data['type'] = siscon::input('type');
        $this->_data['did'] = siscon::input('did');
        $this->template('update');
	}
	public function update_post_ajax()
	{
        $data['type'] = siscon::input('type');
        $data['did'] = siscon::input('did');
        //if($_SERVER['HTTP_REFERER'] != siscon::link('comment-' . $this->_data['type']))
        //{
            //siscon::error('错误的来源');
        //}
        $data['name'] = siscon::input('name');


        $data['content'] = siscon::input('content');
        if(!$data['content'])
        {
		    siscon::error('点评内容不能为空');
        }
        $exists = $data;
        if($user = siscon::core('save')->init()->get('user'))
        {
            $data['uid'] = $user['id'];
        }
        else
        {
            $data['uid'] = -1;
        }
        $data['weibo'] = siscon::input('weibo');
        $data['ip'] = siscon::ip();
        $qip = siscon::core('qip');
        //用户ip归属地
        $IpAddress      = $qip->getaddress($data['ip']);
        $data['city']      = $IpAddress['area1'] . '#' . $IpAddress['area2'];

        $id = siscon::model('comment')->eupdate($data, $data);

        siscon::out(1);
	}
	public function list_get_ajax()
	{
        $this->_data['user'] = siscon::core('save')->init()->get('user');
        $where['type'] = siscon::input('type', 1);
        $where['did'] = siscon::input('did');
        $this->_data['state'] = $where['state'] = siscon::input('state', 2);
        $page['template'] = 'front/turn/comment';
        $page['maxnum'] = 7;
        $this->_data['did'] = $where['did'];
        $this->_data['type'] = $where['type'];
        $this->_data['list'] = siscon::model('comment')->all($where, $page);
        $this->template('list');
	}
}
