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
 * @NAME interface 页面接口类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */
ignore_user_abort(true);//忽略与用户的断开 
set_time_limit(0);
class Happy_Interface extends Module
{
	//浏览器启动cron
	public function cron_act()
	{
		
	}
	
	//定时抓取数据
	public function load_get()
	{
		while(true)
		{
			$this->get_get();
		}
	}
	
    //定期抓取数据
    public function get_get()
    {
		$this->_upload = siscon::core('upload');
		
		$id = siscon::input('id');
		if($id > 0)
		{
			$where['id'] = $id;
		}
		$where['state'] = 1;
		$where['status'] = 1;
		$where['sdate+second^<='] = time();
		$data = siscon::model('happy_config')->all($where);
		
		//print_r($data);die;
		if($data)
		{
			foreach($data as $k => $v)
			{
				siscon::model('happy_config')->update(array('status' => 2, 'sdate' => time()), array('id' => $v['id']));
				$v['site'] = siscon::text_decode($v['site']);
				$v['page'] = siscon::text_decode($v['page']);
				$v['site_rule'] = siscon::text_decode($v['site_rule']);
				$v['pic_rule'] = siscon::text_decode($v['pic_rule']);
				$v['name_rule'] = siscon::text_decode($v['name_rule']);
				$v['content_rule'] = siscon::text_decode($v['content_rule']);
				$v['date_rule'] = siscon::text_decode($v['date_rule']);
				$v['url'] = $v['site'];
				
				//print_r($v);die;
			
				$this->_get($v);
				
				$status = 1;
				if($v['second'] <= 0)
				{
					$status = 3;
				}
				siscon::model('happy_config')->update(array('status' => $status, 'num' => $v['num'] + 1, 'sdate' => time()), array('id' => $v['id']));
			}
		}
		else
		{
			sleep(10);
		}
		
		return;
	}
	
	
	private function _get($config, $page = 1)
	{
		$type = siscon::input('type');
		list($temp, $result) = $this->_match($config['url'], $config['site_rule']);
		if($result && $result[1])
		{
			foreach($result[1] as $k => $v)
			{
				list($html, $name) = $this->_match($v, $config['name_rule'], 'source_url');				
				
				if($name && $name[1] && isset($name[1][0]))
				{
					list($temp, $pic) = $this->_match($html, $config['pic_rule']);
					
					if($config['content_rule'])
					{
						list($temp, $content) = $this->_match($html, $config['content_rule']);
					}
					
					$cdate = '';
					if($config['date_rule'])
					{
						list($temp, $date) = $this->_match($html, $config['date_rule']);
					}
					
					if($pic && $pic[1])
					{
						# 将名称入库并生成一个id
						$data_id = $this->_data($name[1][0], $result[3][$k], $config['id'], $pic[1], $config['url'], $v, $config['cate_id'], $config['type']);
						
						if($data_id)
						{
							$img = array();
							if($content && $content[1])
							{
								foreach($content[1] as $a => $b)
								{
									$img[] = '<p>'.$b.'</p>';
								}
							}
							if($date && $date[1] && $date[1][0])
							{
								$cdate = $date[1][0];
							}
							foreach($pic[1] as $i => $j)
							{
								$title = '';
								if(isset($pic[2]) && $pic[2][$i])
								{
									$title = $pic[2][$i];
								}
								
								$j = $this->_content($title, $j, $i, $data_id, $config['id'], $cdate, $config['type']);
								
								$img[] = '<p><img src="'.siscon::pic($j).'" alt="'.$title.'" /></p>';
							}
							
							$this->_data_content($data_id, $img, $cdate);
						}
					}
				}
			}
			
			if($config['page'])
			{
				$config_page = $config['page'];
				sleep(2);
				$max = false;
				if(strstr($config['page'], '|'))
				{
					$temp = explode('|', $config['page']);
					$config_page = $temp[0];
					$max = $temp[1];
				}
				$page = $page + 1;
				if($max && $page > $max)
				{
					# 最多只能跑这个页数的数据
				}
				else
				{
					$config['url'] = $config['site'] . '' . str_replace('(*)', $page, $config_page);

					$this->_get($config, $page);
				}
			}
		}
		sleep(2);
	}
	
	# 生成内容
	private function _content($name, $pic, $reorder, $data_id, $config_id, $cdate, $type)
	{
		$data['name'] = $name;
		$data['spic'] = $pic;
		$data['config_id'] = $config_id;
		$data['data_id'] = $data_id;
		$data['reorder'] = $reorder;

		$exists = $data;
		
		$data['pic'] = $pic;
		if($cdate) $data['cdate'] = siscon::maketime($cdate);
		$data['mdate'] = $data['zdate'] = time();
		
		$model = 'pic';
		if($type == 1)
		{
			$model = 'pic';
		}
		
		$id = siscon::model('happy_data_' . $model)->eupdate($exists, $data, true);
		
		if($id > 0)
		{
			$update['pic'] = $this->_upload($pic, $id);
			
			siscon::model('happy_data_' . $model)->update($update, array('id' => $id));
			
			return $update['pic'];
		}
		
		return $data['pic'];
	}
	
	# 上传图片
	private function _upload($pic, $id)
	{
		$config['type'] = 'img';
		$config['name'] = $pic; 
		$config['filepath'] = 'happy_pic';
		$config['id'] = $id;

		$info = $this->_upload->save($config);
		
		$info['view_file'] = str_replace(SIS_WRITE_ROOT . 'view/', '', $info['view_file']);
		
		return $info['view_file'];
	}
	
	# 保存数据的内容
	private function _data_content($id, $content, $cdate)
	{
		$update['content'] = implode('', $content);
		if($cdate) $update['cdate'] = siscon::maketime($cdate);
		
		siscon::model('happy_data')->update($update, array('id' => $id));
	}
	
	# 将得到的数据生成一份保存下来
	private function _data($name, $pic, $config_id, $content, $baseurl, $url, $cate_id, $type)
	{
		$data['name'] = $name;
		$data['spic'] = $pic;
		$data['config_id'] = $config_id;
		$data['type'] = $type;
		$data['num'] = count($content);
		$data['source_base_url'] = $baseurl;
		$data['source_url'] = $url;

		$exists = $data;
		
		$data['pic'] = $pic;
		$update['cdate'] = $update['mdate'] = $data['zdate'] = time();
		$data['status'] = 2;
		$data['cate_id'] = $cate_id;
		
		$id = siscon::model('happy_data')->eupdate($exists, $data, true);
		
		if($id > 0)
		{
			$update['pic'] = $this->_upload($pic, $id);
			
			siscon::model('happy_data')->update($update, array('id' => $id));
		}
		
		return $id;
	}
	
	private function _match($data, $rule, $col = false)
	{
		if(!strstr($data, '<head>'))
		{
			if($col)
			{
				if(siscon::model('happy_data')->exists(array($col => $data)))
				{
					return array();
				}
			}
			sleep(1);
			$http = siscon::core('http');
			$data = $http->get($data);
			
			$data = iconv('GB2312', 'UTF-8', $data);
		}
		
		preg_match_all('/' . $rule . '/', $data, $result);
			
		return array($data, $result);
	}
}
