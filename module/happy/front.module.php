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

	public function home_get()
	{
		echo 1;die;
	}

    public function all_get()
	{
        $page['template'] = 'front/turn/list';
        $page['max'] = 10;
        $this->_data['list'] = siscon::model('happy_data', 'happy')->all(array('status' => 1, 'cdate^<=' => time(), 'order^cdate' => 'desc'), $page, false);
        
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
