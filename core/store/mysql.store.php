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
 * @NAME mysql mysql类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Mysql
{
    /**
     * @desc 数据库连接
     * @var object
     */
    private $_connection;

    /**
     * @desc 配置
     * @var array
     */
    private $_config;

    /**
     * @desc 数据库连接
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    private function _connect()
    {
        if(isset($this->_connection[$this->_config['key']])) return;

        $dsn['type'] = $this->_config['config']['type'];
        $dsn['host'] = $this->_config['config']['host'];
        $dsn['port'] = $this->_config['config']['port'];
        $dsn['dbname'] = $this->_config['config']['dbname'];

        foreach($dsn as $key => $val)
        {
            $dsn[$key] = "$key=$val";
        }

        $dsnList = 'mysql:' . implode(';', $dsn);

        try
        {
            $this->_connection[$this->_config['key']] = new PDO($dsnList, $this->_config['config']['username'], $this->_config['config']['password']);
            //$this->_connection[$this->_config['key']]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_connection[$this->_config['key']]->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
            $this->_connection[$this->_config['key']]->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch(PDOException $e)
        {
            if(strstr($e->getMessage(),'Unknown database'))
            {
                $link = @mysql_connect($this->_config['config']['host'], $this->_config['config']['username'], $this->_config['config']['password']);
                @mysql_query("CREATE DATABASE `".$this->_config['config']['dbname']."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;", $link);
                @mysql_close($link);
                $this->_connect();
            }
            else
            {
                $this->_error($e->getMessage());
            }
        }

        $this->_prepare("set names '".$this->_config['config']['charset']."'");
        $this->_create();
        //$this->_log('connected mysql:' . $this->_config['config']['host']);
    }
    
    /**
     * @desc 数据库连接
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function close($data)
    {
		$this->_connection[$this->_config['key']] = null;
	}


    /**
     * @desc 插入数据
     * @param data(array) 传输的数据
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function insert($data)
    {
        $this->_config = $data;

        $value = $data['store']->loadClass('value');

        $this->_connect();

        if(isset($data['data'][0]))
        {
            $this->_connection[$this->_config['key']]->beginTransaction();

            $insert['db'] = $data['table'];

            foreach($data['data'] as $k => $v)
            {
                foreach($data['config']['col'] as $i => $j)
                {
                    if(!$v[$i] && $k != 'insert')
                    {
                        $v[$i] = $value->get($i, $v[$i]);
                    }
                }

                $insert['data'] = $v;
                $this->_connection[$this->_config['key']]->exec($data['store']->loadClass('sql')->config('insert', $insert)->get());
            }

            return $this->_connection[$this->_config['key']]->commit();
        }
        else
        {
            foreach($data['config']['col'] as $k => $v)
            {
                if(!isset($data['data'][$k]) && $k != 'insert')
                {
                    $data['data'][$k] = $value->get($k, '');
                }
            }

            $this->_connection[$this->_config['key']]->exec($data['store']->loadClass('sql')->config('insert', $data)->get());

            return $this->_connection[$this->_config['key']]->lastInsertId();
        }
    }

    /**
     * @desc 查询数据
     * @param data(array) 传输的数据
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function select($data)
    {
        $this->_config = $data;

        $this->_connect();

        if(isset($data['where']['count']))
        {
            $count = $data['where']['count'];
            unset($data['where']['count']);
        }

        if(empty($where['order^id']))
        {
            $where['order^id'] = 'desc';
        }

        $send['where'] = $data['where'];
        $send['table'] = $data['table'];
        $send['data']  = isset($data['data']) ? $data['data'] : '';

        
        $sql = $data['store']->loadClass('sql')->config('select', $send)->get();

        # 增加数据缓存
        $cache_state = false;
        if(isset($data['cache']) && $data['cache'] == true)
        {
			$cache_state = $data['cache'];
		}
        if($cache_state == true)
        {
			$key = md5($sql);
			$cache = new Cache('store_cache');
			$data = $cache->get($key);
			if($data)
			{
				return $data;
			}
		}
        
        if(isset($data['sql']) && $data['sql'] == true)
        {
			print_r($sql);die;
		}

        if(!isset($data['page']) || (isset($data['page']) && !$data['page']))
        {
			if(isset($this->_config['id']) && $this->_config['id'])
			{
				$data = $this->_prepare($sql)->fetchAll(PDO::FETCH_UNIQUE|PDO::FETCH_GROUP);
			}
			else
			{
				$data = $this->_prepare($sql)->fetchAll(PDO::FETCH_ASSOC);
			}
        }
        else
        {
			$page = $data['page'];
            $page['sql'] = $sql;
            $page['db'] = $this;
            $class = siscon::core('page');
            $data = $class->config($page)->get();
            $sql = $data['sql'];
        }

        if($cache_state == true) $cache->set($key, $data);


        return (isset($count) && $count) ? count($data) : $data;
    }

    /**
     * @desc 获取一条数据
     * @param data(array) 传输的数据
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    function selectOne($data)
    {
        $this->_config = $data;

        $this->_connect();

        $send['where'] = isset($data['where']) ? $data['where'] : array();
        $send['table'] = $data['table'];
        $send['data']  = isset($data['data']) ? $data['data'] : '';

        $sql = $data['store']->loadClass('sql')->config('select', $send)->get();

        # 增加数据缓存
        $cache_state = false;
        if(isset($data['cache']) && $data['cache'] == true)
        {
			$cache_state = $data['cache'];
		}
        if($cache_state == true)
        {
			$key = md5($sql);
			$cache = new Cache('store_cache');
			$data = $cache->get($key);
			if($data)
			{
				return $data;
			}
		}

        $data = $this->_prepare($sql)->fetch();

        if($cache_state == true) $cache->set($key, $data);

        return $data;
    }

    
    /**
     * @desc 更新数据
     * @param data(array) 传输的数据
     * @param store(object) 传输的数据
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function update($data)
    {
        $this->_config = $data;

        $this->_connect();

        $send['where'] = $data['where'];
        $send['table'] = $data['table'];
        $send['data']  = $data['data'];

        $sql = $data['store']->loadClass('sql')->config('update', $send)->get();

        $this->_connection[$this->_config['key']]->exec($sql);

        return $this->_connection[$this->_config['key']]->lastInsertId();
    }

    /**
     * @desc 删除
     * @param data(array) 传输的数据
     * @param store(object) 传输的数据
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function delete($data)
    {
        $this->_config = $data;

        $this->_connect();

        $send['where'] = $data['where'];
        $send['table'] = $data['table'];

        $sql = $data['store']->loadClass('sql')->config('delete', $send)->get();

        $this->_prepare($sql);
        
        return $this->_connection[$this->_config['key']]->lastInsertId();
    }

    /**
     * @desc 查询数据
     * @param sql(string) sql语句
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function fetchAll($sql, $id = '')
    {
        return $this->query(array('sql' => $sql));
    }

    /**
     * @desc 查询数据
     * @param sql(string) sql语句
     * @param method(string) 查询方式
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function query($data)
    {
		if(!$this->_config)
		{
			$this->_config = $data;
			$this->_connect();
		}
		$sql = $data['sql'];
		$method = isset($data['method']) ? $data['method'] : 'fetchAll';
		
		if(isset($this->_config['id']) && $this->_config['id'])
		{
			return $this->_prepare($sql)->$method(PDO::FETCH_BOTH, $this->_config['id']);
		}
		else
		{
			return $this->_prepare($sql)->$method();
		}
        
    }

    /**
     * @desc 获取最大数
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    public function fetchSclare($sql)
    {
        //'SELECT found_rows()'
        return $this->query(array('sql' => $sql,  'method' => 'fetchColumn'));
    }

    /**
     * @desc 建表
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    private function _create()
    {
		//return false;
        if($this->_config['config']['create'] == 1)
        {
            $file = $this->_config['store']->createPath(SIS_WRITE_ROOT . 'store' . DIRECTORY_SEPARATOR) . $this->_config['key'] . '_'. $this->_config['table'];
            if(file_exists($file))
            {
                return;
            }

            $send['table'] = $this->_config['table'];
            $send['data'] = $this->_config['config']['col'];

            $sql = $this->_config['store']->loadClass('sql')->config('create', $send)->get();

            $this->_connection[$this->_config['key']]->exec($sql);

            file_put_contents($file, 'create');
        }
    }

    /**
     * @desc 执行sql
     * @author leo(suwi.bin)
     * @date 2012-05-8
     */
    private function _prepare($sql)
    {
        $stmt = $this->_connection[$this->_config['key']]->prepare($sql);

        $stmt->execute();

        return $stmt;
    }

    /**
     * @desc 错误记录
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _error($error)
    {
        //Debug::log("store_mysql error", $error, "store_mysql");
        echo $error;die;
    }

    /**
     * @desc 日志记录
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _log($msg)
    {
        //Debug::log("store_mysql log", $msg, "store_mysql");
    }
}
