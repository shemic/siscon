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
 * @NAME manage 基本管理类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Happy_Manage extends Module
{
    /**
     * @TYPE var
     * @NAME model 自动载入的数据模型
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    protected $_model = 'happy_config';

	public function home_get()
	{
		echo 1;die;
	}

    /**
     * @TYPE function
     * @NAME list 远程抓取配置列表
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
            $where['site^like^or'] = $search;
            
            $this->_data['search'] = $search;
        }

        
		$this->_data['list'] = $this->_model->all($where, $page);
		$w['order^reorder,mdate'] = 'asc,desc';
        $w['status!'] = 2;
		$this->_data['cate'] = siscon::model('happy_cate')->allkey($w, 'id', false, false ,false);
		

		$this->template('list');
	}

    /**
     * @TYPE function
     * @NAME add 新增/更新远程抓取配置
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = $this->_model->one($id);
		    if($this->_data['info'])
		    {
				$this->_data['info']['site'] = base64_decode($this->_data['info']['site']);
				$this->_data['info']['page'] = base64_decode($this->_data['info']['page']);
				$this->_data['info']['site_rule'] = base64_decode($this->_data['info']['site_rule']);
				$this->_data['info']['pic_rule'] = base64_decode($this->_data['info']['pic_rule']);
				$this->_data['info']['name_rule'] = base64_decode($this->_data['info']['name_rule']);
				$this->_data['info']['content_rule'] = base64_decode($this->_data['info']['content_rule']);
				$this->_data['info']['date_rule'] = base64_decode($this->_data['info']['date_rule']);
			}
        }
        
        $w['order^reorder,mdate'] = 'asc,desc';
        $w['status!'] = 2;
		$this->_data['cate'] = siscon::model('happy_cate')->allkey($w, 'id', false, false ,false);
		$this->_data['type'] = array
        (
			array('id' => 1, 'name' => '图片'),
			array('id' => 2, 'name' => '视频'),
			array('id' => 3, 'name' => '文字'),
        );

		$this->template('update');
	}

    /**
     * @TYPE function
     * @NAME add 新增远程抓取配置
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
        $data['site'] = siscon::text_encode(siscon::input('site'));
        if(!$data['site'])
        {
		    siscon::error('配置网址不能为空');
        }
        $data['site_rule'] = siscon::text_encode(siscon::input('site_rule'));
        if(!$data['site_rule'])
        {
		    siscon::error('网址规则不能为空');
        }
        $data['name_rule'] = siscon::text_encode(siscon::input('name_rule'));
        if(!$data['name_rule'])
        {
		    siscon::error('标题规则不能为空');
        }
        $data['pic_rule'] = siscon::text_encode(siscon::input('pic_rule'));
        if(!$data['pic_rule'])
        {
		    siscon::error('图片规则不能为空');
        }

        $exists = $data;

		if(siscon::input('content_rule'))
		{
			$data['content_rule'] = siscon::text_encode(siscon::input('content_rule'));
		}
		if(siscon::input('date_rule'))
		{
			$data['date_rule'] = siscon::text_encode(siscon::input('date_rule'));
		}
		if(siscon::input('cate_id'))
		{
			$data['cate_id'] = siscon::input('cate_id');
		}
		if(siscon::input('type'))
		{
			$data['type'] = siscon::input('type');
		}
		$data['second'] = siscon::input('second');
		if($data['second'] > 0)
		{
			$data['status'] = 1;
		}
		if(siscon::input('page'))
		{
			$data['page'] = siscon::text_encode(siscon::input('page'));
		}
        if(siscon::input('sdate')) $data['sdate'] = siscon::maketime(siscon::input('sdate'));
        
        //print_r($data);die;

        $this->_model->eupdate($exists, $data);

        siscon::out('操作成功');
	}
	
	
	/**
     * @TYPE function
     * @NAME list 数据列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function data_list_get_ajax()
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
		$search['order'] = $search['order'] ? $search['order'] : 2;
		$this->_data['order'] = array
        (
			1 => array
			(
				'name' => '按照数据发布时间正序',
				'value' => array('cdate', 'asc'),
			),
			2 => array
			(
				'name' => '按照数据发布时间倒序',
				'value' => array('cdate', 'desc'),
			),
            3 => array
			(
				'name' => '按照更新时间正序',
				'value' => array('mdate', 'asc'),
			),
			4 => array
			(
				'name' => '按照更新时间倒序',
				'value' => array('mdate', 'desc'),
			),
            5 => array
			(
				'name' => '按照栏目id正序',
				'value' => array('cate_id', 'asc'),
			),
			6 => array
			(
				'name' => '按照栏目id倒序',
				'value' => array('cate_id', 'desc'),
			),
			7 => array
			(
				'name' => '按照编号正序',
				'value' => array('id', 'asc'),
			),
			8 => array
			(
				'name' => '按照编号倒序',
				'value' => array('id', 'desc'),
			),
			10 => array
			(
				'name' => '按照生成时间正序',
				'value' => array('zdate', 'asc'),
			),
			11 => array
			(
				'name' => '按照生成时间倒序',
				'value' => array('zdate', 'desc'),
			),
        );
        $order = true;
        if($search)
        {
			if($search['cate_id'] > 0)
			{
				$cate_list = siscon::model('happy_cate')->allkey(array('cate_id' => $search['cate_id']));
				if($cate_list)
				{
					$cate = siscon::str($cate_list);
					$where['cate_id^in'] = $cate['id'];
				}
				else
				{
					$where['cate_id'] = $search['cate_id'];
				}
			}
			if($search['type'] > 0)
			{
				$where['type'] = $search['type'];
			}
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
				$where['name^like'] = $search['name'];
			}
			if($search['order'] > 0)
			{
				$where['order^' . $this->_data['order'][$search['order']]['value'][0]] = $this->_data['order'][$search['order']]['value'][1];
                $order = false;
			}
			$this->_data['search'] = $search;
            //print_r($where);
        }

		$this->_data['list'] = siscon::model('happy_data')->all($where, $page, $order);
		
        $w['order^reorder,mdate'] = 'asc,desc';
        $w['status!'] = 2;
        $this->_data['cate'] = siscon::model('happy_cate')->allkey($w, 'id', false, false ,false);
        $this->_data['type'] = array
        (
			array('id' => 1, 'name' => '图片'),
			array('id' => 2, 'name' => '视频'),
			array('id' => 3, 'name' => '文字'),
        );
        
        $this->_data['status'] = array
        (
			array('id' => 1, 'name' => '已发布'),
			array('id' => 2, 'name' => '未发布'),
			//array('id' => 3, 'name' => '定时发布'),
        );
        
        
		$this->template('data_list');
	}
	
	/**
     * @TYPE function
     * @NAME add 新增/更新数据
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function data_update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = siscon::model('happy_data')->one($id);
        }

        # 获取栏目
        $this->_data['cate'] = siscon::model('happy_cate')->all();

		$this->template('data_update');
	}
	
	/**
     * @TYPE function
     * @NAME add 更新数据
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function data_col_update_post_ajax()
	{
		$col = siscon::input('col');
		$where['id'] = siscon::input('id');
		$data[$col] = siscon::input('value');
		if($col == 'cdate')
		{
			$data[$col] = siscon::maketime($data[$col]);
		}
		if($data[$col] > 0)
		{
			$data['mdate'] = time();
			$id = siscon::model('happy_data')->update($data, $where);
		}
        
        siscon::out('操作成功');
	}

    /**
     * @TYPE function
     * @NAME add 更新数据
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function data_update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('标题不能为空');
        }
		$data['content'] = siscon::input('content');
        if(!$data['content'])
        {
		    siscon::error('内容不能为空');
        }

        $exists = $data;

        $data['cate_id'] = siscon::input('cate_id');
        $data['content'] = str_replace('\&#39;', '', siscon::input('content'));
        $data['pic'] = siscon::input('pic');
        $data['status'] = siscon::input('status', 1);
        
        if(siscon::input('stime')) $data['stime'] = siscon::maketime(siscon::input('stime'));
        if(siscon::input('cdate')) $data['cdate'] = siscon::maketime(siscon::input('cdate'));
        
        if(!siscon::input('id'))
        {
			$data['zdate'] = time();
		}

        $id = siscon::model('happy_data')->eupdate($exists, $data);
        
        siscon::out('操作成功');
	}
	
	/**
     * @TYPE function
     * @NAME add 删除栏目
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function data_delete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    siscon::model('happy_data')->delete($id);
        }
	}
	
	
	/**
     * @TYPE function
     * @NAME list 内容列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function content_list_get_ajax()
	{
        $where = array();
        $where['data_id'] = siscon::input('data_id', -1);
        $type = siscon::input('type', 1);
        
        $where['order^reorder,mdate'] = 'asc,desc';
        
        if($type == 1)
		{
			$model = 'pic';
		}
		
		$this->_data['list'] = siscon::model('happy_data_' . $model)->all($where, false, false);
        
		$this->template('content_list_' . $model);
	}
	
	/**
     * @TYPE function
     * @NAME list 栏目列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function cate_list_get_ajax()
	{
        $where = array();
        $where['order^reorder,mdate'] = 'asc,desc';
		$this->_data['list'] = siscon::model('happy_cate')->all($where, false, false);
        
		$this->template('cate_list');
	}

	/**
     * @TYPE function
     * @NAME add 新增/更新栏目
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function cate_update_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    $this->_data['info'] = siscon::model('happy_cate')->one($id);
        }
        $where['cate_id'] = -1;
        $this->_data['list'] = siscon::model('happy_cate')->all($where);
        //$this->_data['style'] = siscon::$global['define']['cate']['style'];
		$this->template('cate_update');
	}

    /**
     * @TYPE function
     * @NAME add 删除栏目
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function cate_delete_get_ajax()
	{
        $id = siscon::input('id', -1);
        if($id > 0)
        {
		    siscon::model('happy_cate')->delete($id);
        }
	}
	
	/**
     * @TYPE function
     * @NAME add 更新栏目
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function cate_update_post()
	{
        $data['name'] = siscon::input('name');
        if(!$data['name'])
        {
		    siscon::error('栏目名称不能为空');
        }
		$data['key'] = siscon::input('key');
        if(!$data['key'])
        {
		    siscon::error('栏目key不能为空');
        }
        $data['cate_id'] = siscon::input('cate_id');

        $exists = $data;
        $data['reorder'] = siscon::input('reorder');
        $data['style'] = siscon::input('style');
        $data['info'] = siscon::input('info');
        $data['status'] = siscon::input('status', 1);

        $state = siscon::model('happy_cate')->eupdate($exists, $data);
        
        siscon::out('操作成功');
	}
}
