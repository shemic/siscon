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
 * @NAME page 分页类
 * @AUTHOR LEO
 * @TIME 2013/10/1
 */

class Page
{
    /**
     * @TYPE var
     * @NAME global 核心全局变量
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private $_config = null;

    /**
     * @TYPE function
     * @NAME config 配置函数
     * @PARAM config(array)
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function config($config)
    {
        $this->_config = $config;
        $this->_config['pagename']    = (isset($config['pagename']) && $config['pagename']) ? $config['pagename'] : 'pageturn';
        $this->_config['maxpage']     = (isset($config['maxpage']) && $config['maxpage']) ? $config['maxpage'] : 10;//显示的页数
        $this->_config['maxnum']      = (isset($config['maxnum']) && $config['maxnum']) ? $config['maxnum'] : 20;//每页的记录数
        $this->_config['curpage']     = $this->_config['current'] = (isset($config['current']) && $config['current']) ? intval($config['current']) : 1;//当前页数
        $this->_config['path']        = (isset($config['path']) && $config['path']) ? $config['path'] : '';
        $this->_config['smarty']      = true;
        if(isset($config['template']) && $config['template'])
        {
            $this->_config['template']    = strstr($config['template'], 'turn') ? $config['template'].'.html' : siscon::$global['template'] . '/turn/' . $config['template'].'.html';
        }
        else
        {
            $this->_config['template'] = false;
        }
        $this->_config['id']          = (isset($config['id']) && $config['id']) ? $config['id'] : false;
        $this->_config['totalpage']   = 1;
        $this->_config['suffix']      = (isset($config['suffix']) && $config['suffix']) ? $config['suffix'] : '';
        $this->_config['pageturn']    = (isset($config['pageturn']) && $config['pageturn']) ? $config['pageturn'] : '&' . $this->_config['pagename'] . '=';
        $this->_config['return']      = (isset($this->_config['return']) && isset($this->_config['return'])) ? $this->_config['return'] : 'string';
        $this->_config['sql']         = isset($this->_config['sql']) ? $this->_config['sql'] : '';
        //当前页数据量
        $this->_config['currentnum']  = 0;
        return $this;
    }

    /**
     * @TYPE function
     * @NAME _handle 分页处理程序
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    public function get()
    {
        return array('data' => $this->_handle(),'page' => $this->_turn(),'total' => $this->_config['totalnum'],'maxpage'=>$this->_config['totalpage'],'sql' => $this->_config['sql'], 'currentnum' => $this->_config['currentnum'], 'current' => $this->_config['current']);
    }

    /**
     * @TYPE function
     * @NAME _handle 分页处理程序
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private function _handle()
    {
        $this->_load();
        $this->_write();

        $this->_config['offsetnum']   = $this->_config['maxnum'] * ($this->_config['current']-1);

        $this->_config['offsetpage']  = $this->_config['maxnum']+$this->_config['offsetnum'];
        if(isset($this->_config['dbtype']) && $this->_config['dbtype'] == 'data')
        {
            $this->_config['sql'] = '*';
            $data = array($this->_config['offsetnum'], $this->_config['maxnum']);
            $this->_config['totalnum'] = $this->_config['dbCount'];

            if(isset($this->_config['dbData']))
            {
                $this->_config['dbData'] = array_values($this->_config['dbData']);
                $data = array();
                # 分页数据处理
                if($this->_config['totalnum'] > $this->_config['maxnum'])
                {
                    $total = $this->_config['maxnum']+$this->_config['offsetnum'];
                    for($i=$this->_config['offsetnum']; $i<$total; $i++)
                    {
                        if(isset($this->_config['dbData'][$i]) && $this->_config['dbData'][$i])$data[] = $this->_config['dbData'][$i];
                    }
                }
                else
                {
                    if($this->_config['current'] == 1)
                    {
                        $data = $this->_config['dbData'];
                    }
                    
                }
            }
        }
        elseif(isset($this->_config['total']))
        {
			$this->_config['totalnum'] = $this->_config['total'];
			return array();
		}
        else
        {
            $this->_config['sql'] = str_ireplace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $this->_config['sql']).' LIMIT '.$this->_config['offsetnum'].', '.$this->_config['maxnum'].'';
            //echo $this->_config['sql'];die;
            $data = $this->_config['db']->fetchAll($this->_config['sql'], $this->_config['id']);
            $this->_config['totalnum'] = $this->_config['db']->fetchSclare('SELECT found_rows()');
            $this->_config['currentnum'] = count($data) - 1;
        }
        return $data;
    }

    /**
     * @TYPE function
     * @NAME _handle 分页处理程序
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private function _turn()
    {
        if($this->_config['totalnum'] < 1)
        {
            return;
        }
        $this->_config['totalpage']    = ceil($this->_config['totalnum']/$this->_config['maxnum']);
        $this->_config['curpage']      = ($this->_config['totalpage'] < $this->_config['curpage']) ? $this->_config['totalpage'] : $this->_config['curpage'];
        $this->_config['offsetpage']   = ($this->_config['offsetpage']-$this->_config['totalnum']>0) ? $this->_config['totalnum'] : $this->_config['offsetpage'];
        $this->_config['turnnext']     = $this->_config['totalpage'];
        
        if($this->_config['template'] == false)
        {
            return $this->_config['totalpage'];
        }

        //开始计算分页
        if($this->_config['totalpage'] <= 1)
        {
            if($this->_config['maxnum'] == $this->_config['totalnum'])
            {
                $totalpage = $this->getTurn(($this->_config['totalpage']));
            }
            else
            {
                $totalpage = $this->getTurn($this->_config['totalpage']);
            }
            return $totalpage;
        }
        if($this->_config['totalnum'] > $this->_config['maxnum'])
        {
            //当前总页数小于等于定义的最大页数
            if($this->_config['totalpage'] <= $this->_config['maxpage'])
            {
                $this->_config['start'] = 1;
                $this->_config['end']   = $this->_config['totalpage'];
            }
            else
            {
                if($this->_config['current'] < intval($this->_config['maxpage']/2))
                {
                    $this->_config['start'] = 1;
                }
                else if($this->_config['current'] <= $this->_config['totalpage']-$this->_config['maxpage'])
                {
                    $this->_config['start'] = $this->_config['current']-intval($this->_config['maxpage']/2);
                }
                else if($this->_config['current'] > $this->_config['totalpage']-$this->_config['maxpage'] && $this->_config['current'] <= $this->_config['totalpage']-intval($this->_config['maxpage']/2))
                {
                    $this->_config['start'] = $this->_config['current']-intval($this->_config['maxpage']/2);
                }
                else if($this->_config['current'] > $this->_config['totalpage']-intval($this->_config['maxpage']/2))
                {
                    $this->_config['start'] = $this->_config['totalpage']-$this->_config['maxpage']+1;
                }
                $this->_config['end'] = $this->_config['start'] + $this->_config['maxpage']-1;
                if($this->_config['start'] < 1)
                {
                    $this->_config['end'] = $this->_config['current']+1-$this->_config['start'];
                    $this->_config['start'] = 1;
                    if(($this->_config['end'] - $this->_config['start']) < $this->_config['maxpage'])
                    {
                        $this->_config['end'] = $this->_config['maxpage'];
                    }
                }
                elseif($this->_config['end'] > $this->_config['totalpage'])
                {
                    $this->_config['start'] = $this->_config['totalpage']-$this->_config['maxpage']+1;
                    $this->_config['end']      = $this->_config['totalpage'];
                }
            }
            if(intval($this->_config['totalnum']%$this->_config['maxnum']) == 0)
            {
                $this->_config['turnnext'] = $this->_config['turnnext']+1;
            }
        }

        if($this->_config['return'] == 'array')
        {
            return $this->_config;
        }

        $this->_config['project'] = isset($this->_config['project']) ? $this->_config['project'] : 'main';
        $this->_config['template'] = SIS_MODULE_ROOT . $this->_config['project'] . '/' . $this->_config['template'];
        ob_start();
        $template = $this->_config;
        include($this->_config['template']);
        $this->_config['turnpage'] = ob_get_contents();
        ob_end_clean();
        return $this->_config['turnpage'];

    }

    private function _value($key, $value = false)
    {
        return $GLOBALS[$key] = $value == false ? (isset($GLOBALS[$key]) ? $GLOBALS[$key] : false) : $value;
    }

    /**
     * @TYPE function
     * @NAME _handle 分页处理程序
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private function _write()
    {
        $_SESSION['pageMemory'][md5($this->_config['sql'])] = $this->_config['current'];
        return;
    }

    /**
     * @TYPE function
     * @NAME _handle 分页处理程序
     * @AUTHOR LEO
     * @TIME 2013/10/1
     */
    private function _load()
    {
        if((isset($_GET[$this->_config['pagename']]) && $_GET[$this->_config['pagename']]) && $_GET[$this->_config['pagename']] > 0)
        {
            if($this->_config['pagename'] == 'start' && isset($_POST['limit']))
            {
                //这个比较特殊的
                $num = intval($_POST[$this->_config['pagename']]/$_POST['limit']);
                $num += 1;
                $_POST[$this->_config['pagename']] = $num;
            }
            return $this->_config['current'] = $_GET[$this->_config['pagename']];
        }
        elseif((isset($_POST[$this->_config['pagename']]) && $_POST[$this->_config['pagename']]) && $_POST[$this->_config['pagename']] > 0)
        {
            if($this->_config['pagename'] == 'start' && isset($_POST['limit']))
            {
                //这个比较特殊的
                $num = intval($_POST[$this->_config['pagename']]/$_POST['limit']);
                $num += 1;
                $_POST[$this->_config['pagename']] = $num;
            }
            return $this->_config['current'] = $_POST[$this->_config['pagename']];
        }
        
        if(defined('AJAX') && AJAX != 1) 
        {
            //当刷新页面时(非ajax)取回记忆数据
            $key = md5($this->_config['sql']);
            if($_SESSION['pageMemory'][$key] > 0) {
                return $this->_config['current'] = $_SESSION['pageMemory'][$key];
            }
        }
        
        return $this->_config['current'];
    }

    public function getTurn($value)
    {
        return '<!--maxpage:'.$value.':maxpage-->';
        return '<input type="hidden" id="maxpage" name="maxpage" value="'.$value.'">';
    }
}
