<?php
/*~:SISCON:~
.---------------------------------------------------------------------------.
|  Software: SISCON                   										|
|   Version: 1.0.0                                                          |
|   Contact: ÔÝÎÞ                                                           |
|      Info: ÔÝÎÞ                                                           |
|   Support: ÔÝÎÞ                                                           |
| ------------------------------------------------------------------------- |
|    Author: Leo (suwibin.yu)                                               |
| Copyright (c) 2013-2018, Leo. All Rights Reserved.                        |
'---------------------------------------------------------------------------'

/**
 * @TYPE class
 * @NAME manage 
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Happy_Front extends Module
{
	//说明文档
	public function doc_get()
	{
		$this->template('doc');
	}
	//底层页
	public function info_get()
	{
		$id = siscon::input('id');
		$json = siscon::input('json', 1);

		$where['id'] = $id;
		//$where['status'] = 1;
        $info = siscon::model('happy_data', 'happy')->exists($where);
        
        if($info)
        {
			if($info['pic']) $info['pic'] = siscon::pic($info['pic']);
				
			$info['date'] = date('Y-m-d', $info['cdate']);
				
			$info['time'] = date('Y-m-d H:i:s', $info['cdate']);
				
			$result['data'] = $info;
		}
		else
		{
			$result['data'] = array();
		}
		
        if($json == 1)
        {
			$result = json_encode($result);
		}
        
        print_r($result);
	}
	//列表页
	public function home_get()
	{
		$num = siscon::input('num', 10);
		$json = siscon::input('json', 1);
		
		$cate = siscon::input('cate', false);
		
        $page['template'] = 'front/turn/list';
        $page['project'] = 'happy';
        $page['maxnum'] = $num;
        $page['path'] = 'home';
        
        //$where['status'] = 1;
        $where['cdate^<='] = time();
        $where['order^cdate'] = 'desc';
        if($cate > 0)
        {
			$where['cate_id'] = $cate;
		}
        $list = siscon::model('happy_data', 'happy')->all($where, $page, false);
        
        
        $result = array();
        
        if($list['data'])
        {
			$cate = siscon::model('happy_cate', 'happy')->allkey();
			
			foreach($list['data'] as $k => $v)
			{
				if($v['pic']) $list['data'][$k]['pic'] = siscon::pic($v['pic']);
				
				$list['data'][$k]['date'] = date('Y-m-d', $v['cdate']);
				
				$list['data'][$k]['time'] = date('Y-m-d H:i:s', $v['cdate']);
				
				$list['data'][$k]['link'] = siscon::link('interface/info/' . $v['id'] . '/' . $v['type']);
				
				if($cate && $v['cate_id'] > 0 && $cate[$v['cate_id']])
				{
					$list['data'][$k]['cate_name'] = $cate[$v['cate_id']]['name'];
					$list['data'][$k]['cate_link'] = siscon::link('interface/list/' . $v['cate_id'] . '/1');
				}
			}
			$result['data'] = $list['data'];
			
			$result['total'] = $list['total'];
			
			$result['maxpage'] = $list['maxpage'];
			
			$result['currentpage'] = $list['current'];
			
			if($result['currentpage'] >= $result['maxpage'])
			{
				$result['prevpage'] = $result['currentpage'] - 1;
				$result['nextpage'] = 1;
			}
			elseif($result['currentpage'] <= 1)
			{
				$result['prevpage'] = $result['maxpage'];
				$result['nextpage'] = $result['currentpage'] + 1;
			}
			else
			{
				$result['prevpage'] = $result['currentpage'] - 1;
				$result['nextpage'] = $result['currentpage'] + 1;
			}
		}
		else
		{
			$result['data'] = array();
			
			$result['total'] = 0;
			
			$result['maxpage'] = 0;
			
			$result['currentpage'] = 0;
		}
        
        if($json == 1)
        {
			$result = json_encode($result);
		}
        
        print_r($result);
        
        return;
        //$this->template('list');
	}

    public function all_get()
	{
        $page['template'] = 'front/turn/list';
        $page['maxnum'] = 10;
        $page['project'] = 'happy';
        $page['path'] = 'home';
        $this->_data['list'] = siscon::model('happy_data', 'happy')->all(array('cdate^<=' => time(), 'order^cdate' => 'desc'), $page, false);
        
        $this->template('list');
	}

    public function view_get()
	{
		$id = siscon::input('id');
        $type = siscon::input('type');
        
        $page = siscon::input('pageturn', 1);

		$where['id'] = $id;
		//$where['status'] = 1;
        $this->_data['info'] = siscon::model('happy_data', 'happy')->exists($where);
        
        if($this->_data['info'])
        {
			$this->template('view');
		}
	}
}
