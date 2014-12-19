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

class Share_Manage extends Module
{

    /**
     * @TYPE function
     * @NAME list 分享日志列表
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
				'name' => '按照分享时间正序',
				'value' => array('cdate', 'asc'),
			),
			2 => array
			(
				'name' => '按照分享时间倒序',
				'value' => array('cdate', 'desc'),
			),
        );
		$this->_data['platform'] = array(
            '1'         => array('tqq',     "http://v.t.qq.com/share/share.php?"),
            '2'         => array('tsina',   "http://service.weibo.com/share/share.php?"),
            '3'         => array('qzone',   "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?"),
            '4'         => array('renren',  "http://widget.renren.com/dialog/share?"),
            '5'         => array('douban',  "http://www.douban.com/share/service?"),
            '6'         => array('weixin',  ""),
            '7'         => array('mtsina',   "http://service.weibo.com/share/share.php?"),
        );
        $order = true;
        if($search)
        {
			if($search['status'] > 0)
			{
				$where['status'] = $search['status'];
			}
			if($search['platform'])
			{
				$where['platform'] = $search['platform'];
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
				$where['surl^like^and^('] = $search['name'];
				$where['url^like^or^)'] = $search['name'];
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
		$this->_data['list'] = siscon::model('share_log')->all($where, $page, $order);

        
		$this->template('list');
	}
	
	
	/**
     * @TYPE function
     * @NAME list 分享日志列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function reflux_list_get_ajax()
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
				'name' => '按照分享时间正序',
				'value' => array('cdate', 'asc'),
			),
			2 => array
			(
				'name' => '按照分享时间倒序',
				'value' => array('cdate', 'desc'),
			),
        );
		$this->_data['platform'] = array(
            '1'         => array('tqq',     "http://v.t.qq.com/share/share.php?"),
            '2'         => array('tsina',   "http://service.weibo.com/share/share.php?"),
            '3'         => array('qzone',   "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?"),
            '4'         => array('renren',  "http://widget.renren.com/dialog/share?"),
            '5'         => array('douban',  "http://www.douban.com/share/service?"),
            '6'         => array('weixin',  ""),
            '7'         => array('mtsina',   "http://service.weibo.com/share/share.php?"),
        );
        $order = true;
        if($search)
        {
			if($search['status'] > 0)
			{
				$where['status'] = $search['status'];
			}
			if($search['platform'])
			{
				$where['platform'] = $search['platform'];
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
				$where['surl^like^and^('] = $search['name'];
				$where['url^like^or^)'] = $search['name'];
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
		$this->_data['list'] = siscon::model('share_reflux')->all($where, $page, $order);

        
		$this->template('reflux_list');
	}
	
	
	/**
     * @TYPE function
     * @NAME list 分享统计列表
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function total_list_get_ajax()
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
				'name' => '按照分享次数倒序',
				'value' => array('total', 'desc'),
			),
			2 => array
			(
				'name' => '按照分享次数正序',
				'value' => array('total', 'asc'),
			),
			
        );

        $order = true;
        if($search)
        {
			if($search['cate_id'] > 0)
			{
				$cate_list = siscon::model('cate', 'article')->allkey(array('cate_id' => $search['cate_id']));
				if($cate_list)
				{
					$cate = siscon::str($cate_list);
					$where['cate^in'] = $cate['id'];
				}
				else
				{
					$where['cate'] = $search['cate_id'];
				}
			}
			if($search['status'] > 0)
			{
				$where['status'] = $search['status'];
			}
			if($search['platform'])
			{
				$where['platform'] = $search['platform'];
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
				$where['url^like'] = $search['name'];
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
		$this->_data['list'] = siscon::model('share_total')->all($where, $page, $order);

        $w['order^reorder,mdate'] = 'asc,desc';
        $w['status!'] = 2;
        $this->_data['cate'] = siscon::model('cate', 'article')->allkey($w, 'id', false, false ,false);
        
        if($this->_data['list']['data'])
        {
			$array = siscon::str($this->_data['list']['data'], 'article');
			
			$this->_data['article'] = siscon::model('article', 'article')->allkey(array('id^in' => $array['article']), 'id', false, false ,false);
		}
		$this->template('total_list');
	}

}
