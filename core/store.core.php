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
 * @NAME store 存储类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Store
{
    /**
     * @TYPE var
     * @NAME key的配置
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private $_key = '';

    /**
     * @TYPE var
     * @NAME 配置
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private $_config = array();

    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function init($key)
    {
        $this->_key = md5($key);
        $this->_config['key'][$this->_key] = $key;
        $this->_config[$this->_key]['config'] = siscon::config('store', $key);
    }

    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function load($method, $table, $data = array())
    {
        $array = array('select', 'update', 'insert', 'delete', 'selectOne', 'add', 'query', 'close');
        if(in_array($method, $array))
        {
            $data['table']  = $table;
            $data['config'] = $this->_key($table);
            $data['key']    = $this->_key;
            $data['store']  = $this;
            $return = $this->loadClass($this->_config[$this->_key]['config'][$table]['type'])->$method($data);
        }
        else
        {
            $return = array();
        }
        return $return;
    }

	/**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private function _key($table)
    {
		//print_r($this->_config[$this->_key]['config'][$table]);die;
        if(isset($this->_config[$this->_key]['config'][$table]))
        {
            return $this->_config[$this->_key]['config'][$table];
        }
        else
        {
            unset($this->_config['key'][$this->_key]);
            foreach($this->_config['key'] as $k => $v)
            {
                $this->_key = $k;
            }
            return $this->_key($table);
        }
    }

    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function loadClass($name)
    {
        if(!isset($this->_config[$this->_key]['class'][$name]))
        {
			include_once(dirname(__FILE__) . '/store/' . $name . '.store.php');
			
            $className = ucwords($name);

            $this->_config[$this->_key]['class'][$name] = new $className();
        }

        return $this->_config[$this->_key]['class'][$name];
    }

    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function createPath($path)
    {
        if(!is_dir($path))
        {
            mkdir($path, 0775);
            exec('chmod 777 ' . $path);
        }
        return $path;
    }


    /**
     * @TYPE function
     * @NAME __construct 构造函数
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function loopStruct($struct)
    {
        //$struct = base64_decode($struct);
        $list = array('where', 'data');
        if(strstr($struct, '*'))
        {
            $data = explode('*', $struct);
            foreach($list as $k => $v)
            {
                $return[$v] = $this->struct($data[$k]);
            }
        }
        else
        {
            $return = $this->struct($struct);
        }
        return $return;
    }

    /**
     * @desc 对字符串进行结构化解析（单独）
     * @param struct(string) 字符串
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function struct($struct)
    {
        $array = explode('%%', $struct);
        $return = '';
        foreach($array as $k => $v)
        {
            if(isset($v) && $v != '')
            {
                if(strstr($v, '^'))
                {
                    $i = explode('^', $v);
                    if(strstr($i[0], '-'))
                    {
                        $i[0] = str_replace('-', '^', $i[0]);
                    }
                    if(strstr($i[0], '>'))
                    {
                        $i[0] = str_replace('>', '^>', $i[0]);
                    }
                    if(strstr($i[0], '<'))
                    {
                        $i[0] = str_replace('<', '^<', $i[0]);
                    }
                    if(strstr($i[0], 'like'))
                    {
                        $i[0] = str_replace('like', '^like', $i[0]);
                    }
                    $i[1] = $this->parseTime($v, $i[1]);
                    $return[$i[0]] = $i[1];
                }
                else
                {
                    $return[$k] = $v;
                }
            }
        }
        return $return;
    }

    /**
     * @desc 对数组进行结构化反解析（批量）
     * @param array(array) 数组
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function loopUnstruct($array)
    {
        if(isset($array['where']) && isset($array['data']))
        {
            $struct = $this->unstruct($array['where']) . '*' . $this->unstruct($array['data']);
        }
        else
        {
            $struct = $this->unstruct($array);
        }

        return $struct;
    }

    /**
     * @desc 对数组进行结构化反解析（单独）
     * @param array(array) 数组
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function unstruct($array)
    {
        $struct = '';
        foreach($array as $k => $v)
        {
            if(strstr($k, '>') || strstr($k, '<'))
            {
                $k = str_replace('^', '', $k);
            }
            if(strstr($k, '^'))
            {
                $k = str_replace('^', '-', $k);
            }
            $struct .= $k . '^' . $v . '%%';
        }
        
        $struct = ereg_replace('\%%$', '', $struct);
        return $struct;
    }

    /**
     * @desc 解析数据
     * @param array(array) 数组
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function parse($data)
    {
        foreach($data as $k => $v)
        {
            if(strstr($v, '^'))
            {
                $array = explode('^', $v);
                switch($array[0])
                {
                    case 'date':
                        $array[2] = $array[2] == 'time' ? time() : $array[2];
                        $array[3] = isset($array[3]) ? $array[3] : 0;
                        $data[$k] = date($array[1], $array[2]+$array[3]);
                        break;
                    case 'time':
                        break;
                }
            }
        }
        return $data;
    }

    /**
     * @desc 解析时间
     * @param col(string) 字段
     * @param value(string) 值
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function parseTime($col, $value)
    {
        if(strstr($col,'cdate') && strstr($value, ' '))
        {
            $t = explode(' ', $value);
            $k = explode('-', $t[0]);
            $v = explode(':', $t[1]);
            $value = mktime($v[0], $v[1], $v[2], $k[1], $k[2], $k[0]);
        }
        elseif(strstr($col,'cdate') && strstr($value, '-'))
        {
            $t = explode('-', $value);
            $value = mktime($t[3], $t[4], $t[5], $t[1], $t[2], $t[0]);
        }
        return $value;
    }

    /**
     * @desc 错误记录
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _error($error)
    {
        Debug::log("store error", $error, "store");
    }

    /**
     * @desc 日志记录
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _log($msg)
    {
        Debug::log("store log", $msg, "store");
    }
}
