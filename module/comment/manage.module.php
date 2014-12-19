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

class Comment_Manage extends Module
{
    /**
     * @TYPE var
     * @NAME model 自动载入的数据模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_model = 'comment';

	public function home_get()
	{
		echo 1;die;
	}

    /**
     * @TYPE function
     * @NAME list 表
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
				'name' => '按照发布时间正序',
				'value' => array('cdate', 'asc'),
			),
			2 => array
			(
				'name' => '按照发布时间倒序',
				'value' => array('cdate', 'desc'),
			),
        );
        $order = true;
        if($search)
        {
			if($search['uid'] > 0)
			{
				$where['uid'] = $search['uid'];
			}
			if($search['ip'])
			{
				$where['ip'] = $search['ip'];
			}
			if($search['sctime'] > 0)
			{
				$where['cdate^>='] = siscon::maketime($search['sctime']);
			}
			if($search['ectime'] > 0)
			{
				$where['cdate^<='] = siscon::maketime($search['ectime']);
			}
			if($search['content'])
			{
				$where['content^like'] = $search['content'];
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

        //获取用户信息
        if($this->_data['list']['data'])
        {
			$uid = siscon::str($this->_data['list']['data'], 'uid,did');
			$w['id'] = $uid['uid'];
			$this->_data['user'] = siscon::model('user', 'user')->allkey($w);
			
			$wc['id'] = $uid['did'];
			$this->_data['article'] = siscon::model('article', 'article')->allkey($wc, 'id', false, false ,false);
			
			
			$wa['order^reorder,mdate'] = 'asc,desc';
			$wa['status!'] = 2;
			$this->_data['cate'] = siscon::model('cate', 'article')->allkey($wa, 'id', false, false ,false);

		}
		$this->template('list');
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
     * @NAME add 新增/更新图片配置
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

        $this->_data['water'] = siscon::$global['define']['water'];
		$this->template('update');
	}

    /**
     * @TYPE function
     * @NAME add 新增图片配置
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('配置名称不能为空');
        }
        $data['key'] = siscon::input('key');
        if(!$data['key'])
        {
		    siscon::error('配置key不能为空');
        }

        $exists = $data;

        $data['width'] = siscon::input('width');
        $data['height'] = siscon::input('height');
        $data['size'] = siscon::input('size');
        $data['t_width'] = siscon::input('t_width');
        $data['t_height'] = siscon::input('t_height');
        $data['t_type'] = siscon::input('t_type');
        $data['c_width'] = siscon::input('c_width');
        $data['c_height'] = siscon::input('c_height');
        $data['c_type'] = siscon::input('c_type');
        $data['w_type'] = siscon::input('w_type');
        $data['w_pic'] = siscon::input('w_pic');
        $data['quality'] = siscon::input('quality');
        $data['content'] = siscon::input('content');
        $data['status'] = siscon::input('status', 1);

        $this->_model->eupdate($exists, $data);

        siscon::out('操作成功');
	}

}
