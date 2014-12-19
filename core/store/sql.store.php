<?php
/**
 * @sourcename store_sql.class.php
 * @desc 数据通用存储 sql拼装类
 * @author leo(suwi.bin) - bin.yu@condenast.com.cn
 * @date 2012-05-8
 */

class Sql
{
    /**
     * @desc 表名
     * @var string
     */
    private $_table;

    /**
     * @desc 传输的数据
     * @var array
     */
    private $_data;

    /**
     * @desc where条件
     * @var array
     */
    private $_where;

    /**
     * @desc 调用的方法
     * @var string
     */
    private $_method;

    /**
     * @desc 配置数据
     * @param method(string) 调用的方法
     * @param data(array) 包含的数据有table、data、where等
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function config($method, array $data)
    {
        $this->_method    = $method;
        $this->_table     = isset($data['table']) ? $data['table'] : '';
        $this->_data      = isset($data['data']) ? $data['data'] : array();
        $this->_where     = isset($data['where']) ? $data['where'] : array();
        return $this;
    }

    /**
     * @desc 执行方法并获取到拼装好的sql
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    public function get()
    {
        $function  = '_'.$this->_method;
        $sql = $this->$function();
        return $sql;
    }

    /**
     * @desc 拼装insert
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _insert()
    {
        $dataList = $this->_data;
        $cols = $values = '';
        $exists = isset($dataList['exists']) ? $dataList['exists'] : false;
        unset($dataList['exists']);
        unset($this->_data['exists']);
        $where = $this->_where;
        if($where != null)
        {
            $list        = $this->_whereList($where);
            $whereList   = $list['where'];
            //$exists      = 'IF NO EXISTS(SELECT state FROM ' . $this->_table . '' . $whereList . '  )';
        }
        if(true == is_object($this->_data))
        {
            $dataList = get_object_vars($this->_data);
        }
        foreach($dataList as $k => $v)
        {
            //$v = @mysql_real_escape_string(trim($v));
            $cols    .= '`' . trim($k) . '`,';
            if(strstr($v, '"'))
            {
                $v = str_replace('"', '\\\'', $v);
            }

            if(strstr($v, 'session'))
            {
                $v = 'base64_encode->' . base64_encode($v);
            }
            $values  .= '"' . $v . '",';
        }
        $duplicate = $ignore = '';
        
        if($exists == 1)
        {
            //如果数据存在则更新数据
            $duplicate = 'ON DUPLICATE KEY ' . $this->_update(2);
        }
        elseif($exists == 2)
        {
            //如果数据存在，不执行任何操作，不存在则插入
            $ignore = 'IGNORE';
        }

        $sql = 'INSERT ' . $ignore . ' INTO `' . $this->_table . '`(' . substr($cols,0,-1) . ') VALUES (' . substr($values,0,-1) . ') ' . $duplicate;


        $this->_log($sql);

        return $sql;
    }

    /**
     * @desc 拼装update
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _update($state = 1)
    {
		$set = '';
		if($state == 1)
		{
			$set = ' SET ';
		}
        $sql         =  'UPDATE `' . $this->_table . '` ' . $set;
        $dataList    = $this->_data;
        $where       = $this->_where;
        $list        = $this->_whereList($where);
        $whereList   = $list['where'];
        foreach($dataList as $k => $v)
        {
            //$v = @mysql_real_escape_string(trim($v));
            if(strstr($k, '+'))
            {
                $k = str_replace('+', '', $k);
                $sql .= '`'.$k.'` = `'.$k.'` + '.$v.',';
            }
            elseif(strstr($k, '-'))
            {
                $k = str_replace('-', '', $k);
                $sql .= '`'.$k.'` = `'.$k.'` - '.$v.',';
            }
            else
            {
				if(strstr($v, "'"))
				{
					$sql .= "`".$k."` = \"".$v."\",";
				}
				else
				{
					$sql .= '`'.$k.'` = \''.$v.'\',';
				}
            }
        }
        $sql = substr($sql,0,-1) . $whereList;

        $this->_log($sql);

        return $sql;
    }

    /**
     * @desc 拼装select
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _select()
    {
        $where        = $this->_where;
        $data         = $this->_data;
        $list         = $this->_whereList($where);
        $whereList    = isset($list['where']) ? $list['where'] : '';
        $colList      = $this->_colList($data);
        $orderList    = isset($list['order']) ? $list['order'] : '';
        $limitList    = isset($list['limit']) ? $list['limit'] : '';
        $sql          = 'SELECT '.$colList.' FROM `'.$this->_table.'` '.$whereList.$orderList.$limitList;

        $this->_log($sql);
        //echo $sql;
        //echo '<br />';
        return $sql;
    }

    /**
     * @desc 拼装delete
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _delete()
    {
        $where        = $this->_where;
        $list         = $this->_whereList($where);
        $whereList    = $list['where'];
        $sql          = 'DELETE FROM ' . $this->_table . ' '.$whereList;
        $this->_log($sql);
        return $sql;
    }

    /**
     * @desc 拼装optimize
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _optimize()
    {
        $sql          = 'OPTIMIZE TABLE ' . $this->_table . '';
        $this->_log($sql);
        return $sql;
    }

    /**
     * @desc 拼装建表语句
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _create()
    {
        $data           = $this->_data;
        if(isset($data['table'])) unset($data['table']);
        if(isset($data['insert']))
        {
            $insert = $data['insert'];
            unset($data['insert']);
        }
        $cList          = array();
        $iList          = '';
        if(isset($data['index']) && $data['index'])
        {
            $iList        = ', INDEX (`'.str_replace(',','` , `',$data['index']).'`)';
            unset($data['index']);
        }
        $primary = '';
        foreach($data as $k => $v)
        {
            $primary[$k] = '';
            if(strstr($v,' '))
            {
                $com = explode(' ',$v);
                $v = $com[0];
                $primary[$k] = ' comment "'.$com[1].'" ';
            }
            if($k == 'id')
            {
                $primary[$k] = ' unsigned auto_increment primary key ' . $primary[$k];
            }
            $cList[] = '`'.$k.'` '.strtoupper(str_replace('-','(',$v).') '.$primary[$k].'');// not null 
        }
        $sql    = 'DROP TABLE IF EXISTS `'.$this->_table.'`;CREATE TABLE `'.$this->_table.'`('.implode(',', $cList).$iList.')';

        //$sql    = 'CREATE TABLE `'.$this->_table.'`('.implode(',', $cList).$iList.')';
        if(isset($insert) && $insert['col'] && $insert['value'])
        {
            if(strstr($insert['value'], 'time'))
            {
                $insert['value'] = str_replace('time', time(), $insert['value']);
            }
            $sql .= ';INSERT INTO `'.$this->_table.'`('.$insert['col'].') VALUES( ' . str_replace('\'.\'', '\' ),( \'', $insert['value']) . ' )';
        }

        //echo $sql;die;
        $this->_log($sql);
        return $sql;
    }

    /**
     * @desc 拼装where条件
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _whereList($where)
    {
        $whereList = $orderList = $limitList = '';
        if(is_array($where))
        {
            foreach($where as $k => $v)
            {
				//$v = mysql_escape_string($v);
                if(count($where) >= 1 && strpos($k,'^'))
                {
                    $type = explode('^',$k);
                    
                    if(strstr($type[0], '+'))
                    {
                        $a = explode('+', $type[0]);
                        $type[0] = $a[0] . '`+`' . $a[1];
                    }

                    if($type[1] == 'or' || $type[1] == 'and')
                    {
                        if(strpos($type[0],'!'))
                        {   
                            if(strstr($v, ','))
                            {
                                $g = explode(',', $v);
                                foreach($g as $q => $p)
                                {
                                    $whereList .= ' '.strtoupper($type[1]).' `'.str_replace('!','',$type[0]).'` != \''.$p.'\' ';
                                }
                            }
                            else
                            {
                                $whereList .= ' '.strtoupper($type[1]).' `'.str_replace('!','',$type[0]).'` != \''.$v.'\' ';
                            }
                        }
                        else
                        {
                            $whereList .= ' '.strtoupper($type[1]).' `'.$type[0].'` = \''.$v.'\' ';
                        }
                    }
                    if(isset($type[2]))
                    {
						$and = strtoupper($type[2]);
					}
					else
					{
						$and = 'AND';
					}
					
					if(isset($type[3]) && $type[3] == '(')
					{
						$and .= '(';
					}
					
					if(strstr($type[0], '('))
					{
						$col = $type[0];
					}
					else
					{
						$col = ' `'.$type[0].'` ';
					}
					switch($type[1])
					{
						case 'in':
							$whereList .= ' '.$and.' '.$col.' in ('.$v.') ';
							break;
						case 'notin':
							$whereList .= ' '.$and.' '.$col.' not in ('.$v.') ';
							break;
						case '<=':
							$whereList .= ' '.$and.' '.$col.' <= '.$v.' ';
							break;
						case '<*':
							$whereList .= ' '.$and.' '.$col.' <= '.$v.' ';
							break;
						case '>=':
							$whereList .= ' '.$and.' '.$col.' >= '.$v.' ';
							break;
						case '>*':
							$whereList .= ' '.$and.' '.$col.' >= '.$v.' ';
							break;
						case '<':
							$whereList .= ' '.$and.' '.$col.' < '.$v.' ';
							break;
						case '>':
							$whereList .= ' '.$and.' '.$col.' > '.$v.' ';
							break;
						case 'like':
							$whereList .= ' '.$and.' '.$col.' LIKE(\'%'.$v.'%\') ';
							break;
					}
					
					if(isset($type[3]) && $type[3] == ')')
					{
						$whereList .= ')';
					}
					
					/*
                    if($type[1] == 'in')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` in ('.$v.') ';
                    }
                    if($type[1] == 'notin')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` not in ('.$v.') ';
                    }
                    if($type[1] == '<=' || $type[1] == '<*')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` <= '.$v.' ';
                    }
                    if($type[1] == '>=' || $type[1] == '>*')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` >= '.$v.' ';
                    }
                    if($type[1] == '<')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` < '.$v.' ';
                    }
                    if($type[1] == '>')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` > '.$v.' ';
                    }
                    if($type[1] == 'like')
                    {
                        $whereList .= ' '.$and.' `'.$type[0].'` LIKE(\'%'.$v.'%\') ';
                    }
                    if($type[1] == 'count')
                    {
                        $whereList .= ' '.$and.' count(`'.$type[0].'`) as '.$v.' ';
                    }
                    */

                    if($type[0] == 'order')
                    {
                        if(strpos($type[1],','))
                        {
							$g = explode(',', $type[1]);
							$o = explode(',', $v);
							
							$orderList .= ' '.strtoupper($type[0]).' BY ';
							
							foreach($g as $q => $p)
                            {
								if($q > 0)
								{
									$orderList .= ',';
								}
								$orderList .= ' `'.$p.'` '.strtoupper($o[$q]);
                            }
                        }
                        elseif(strpos($type[1],'()'))
                        {
                            $orderList .= ' '.strtoupper($type[0]).' BY '.$type[1].' ';
                        }
                        else
                        {
                            $orderList .= ' '.strtoupper($type[0]).' BY `'.$type[1].'` '.strtoupper($v).' ';
                        }
                    }
                    if($type[0] == 'group')
                    {
                        $orderList .= ' '.strtoupper($type[0]).' BY `'.$type[1].'` ';
                    }
                    if($type[0] == 'limit')
                    {
                        if($v)
                        {
                            $limitList = ' '.strtoupper($type[0]).' '.$type[1].' , '.$v.' ';
                        }
                        else
                        {
                            $limitList = ' '.strtoupper($type[0]).' '.$type[1].' ';
                        }
                    }
                }
                elseif(count($where) > 1)
                {
                    if(strpos($k,'!'))
                    {
                        if(strstr($v, ','))
                        {
                            $g = explode(',', $v);
                            foreach($g as $q => $p)
                            {
                                    $whereList .= ' AND `'.str_replace('!','',$k).'` != \''.$p.'\' ';
                            }
                        }
                        else
                        {
                            $whereList .= ' AND `'.str_replace('!','',$k).'` != \''.$v.'\' ';
                        }
                    }
                    else
                    {
                        $whereList .= ' AND `'.$k.'` = \''.$v.'\' ';
                    }
                    
                }
                else
                {
                    $whereList = ' `'.$k.'` = \''.$v.'\' ';
                }
            }
            $whereList = preg_replace('/^ AND/', ' ', $whereList);
            $whereList = preg_replace('/^ OR/', ' ', $whereList);
        }
        else
        {
            $whereList = $where ? $where : '';
        }
        $whereList = $whereList ? ' WHERE '.$whereList : '';
        $list = array('where' => $whereList,'order' => $orderList,'limit' => $limitList);
        return $list;
    }

    /**
     * @desc 拼装字段
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _colList($data)
    {
        $colList = '';
        if(is_array($data))
        {
            foreach($data as $key => $value)
            {
                if(!is_numeric($key))
                {
                    $colList .= ', '.$key.' AS '.$value.' ';
                }
                else
                {
                    $colList .= ', '.$value.' ';
                }
            }
            $colList = ereg_replace('^,', ' ', $colList);
        }
        else
        {
            $colList = $data ? $data : '*';
        }
        return $colList;
    }

    /* 以下是另外一种拼装方法 */

    /**
     * @desc 拼装select
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _col()
    {
        $col = ' SELECT '.$this->_data.' ';
        return $col;
    }

    /**
     * @desc 拼装where
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _where()
    {
        $where = ' WHERE '.$this->_data.' ';
        return $where;
    }

    /**
     * @desc 拼装join
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _join()
    {
        $join = ' LEFT JOIN '.$this->_data.' ON '.$this->_where.' ';
        return $join;
    }

    /**
     * @desc 拼装from
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _from()
    {
        $from = ' FROM '.$this->_table.' ';
        return $from;
    }

    /**
     * @desc 拼装group
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _group()
    {
        $group = ' GROUP BY '.$this->_data.' ';
        return $group;
    }

    /**
     * @desc 拼装order
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _order()
    {
        $order = ' ORDER BY '.$this->_data.' '.$this->_where;
        return $order;
    }

    /**
     * @desc 拼装limit
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _limit()
    {
        $limit = ' LIMIT '.$this->_data.' ';
        return $limit;
    }

    /**
     * @desc 日志记录
     * @author leo(suwi.bin)
     * @date 2012-03-23
     */
    private function _log($msg)
    {
        //Debug::log("store_sql log", $msg, "store_sql");
    }
}
?>
