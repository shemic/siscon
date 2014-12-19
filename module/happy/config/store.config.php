<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 happy 基本抓取配置表
    'happy_config' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'cate_id' => 'int-11 栏目id',
            'type' => 'int-1 类型1为图片2为视频3为文字',
            'name' => 'varchar-24 配置名',
            'site' => 'varchar-200 配置网址', 
            'page' => 'varchar-200 配置网址分页', 
            'site_rule' => 'varchar-200 网址匹配规则', 
            'name_rule' => 'varchar-200 标题匹配规则', 
            'pic_rule' => 'varchar-200 图片匹配规则', 
            'content_rule' => 'varchar-200 内容匹配规则', 
            'date_rule' => 'varchar-200 时间匹配规则', 
            'second' => 'int-11 抓取间隔秒数',
            'num' => 'int-11 已抓取次数',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'sdate' => 'int-11 抓取时间', 
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
            'status' => 'int-1 状态1为正常2为正在抓取数据3为已完成抓取',
        ),
    ),
    
    //建立一个mysql表 happy 的栏目表
    'happy_cate' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'cate_id' => 'int-11 父级栏目id',
            'name' => 'varchar-24 栏目名',
            'info' => 'varchar-100 栏目介绍',
            'key' => 'varchar-200 模板key,对应前台url,如果为链接则直接外链',
            'reorder' => 'int-11 排序',
            'style' => 'int-1 样式',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
            'status' => 'int-1 状态1为启用2为不启用',
        ),
    ),
    
    //建立一个mysql表 happy 的内容表
    'happy_data' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'config_id' => 'int-11 配置id',
            'cate_id' => 'int-11 栏目id',
            'num' => 'int-11 内容数量',
            'type' => 'int-1 类型1为图片2为视频3为文字',
            'name' => 'varchar-60 标题',
            'content' => 'text-255 内容',
            'pic' => 'varchar-200 封面图片',
            'spic' => 'varchar-200 原图片地址',
            'source_base_url' => 'varchar-255 来源的列表页',
            'source_url' => 'varchar-255 来源的页面',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'zdate' => 'int-11 抓取时间',
            'stime' => 'int-11 发布时间，仅当status=3时有效',
            'state' => 'int-1 状态1为存在2为删除',
            'status' => 'int-1 状态1为发布2为暂时不发布',
        ),
    ),
    
    
    //建立一个mysql表 happy 的内容表下的数据
    'happy_data_pic' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'config_id' => 'int-11 配置id',
            'data_id' => 'int-11 内容id',
            'reorder' => 'int-11 排序',
            'name' => 'varchar-60 标题',
            'pic' => 'varchar-200 图片地址',
            'spic' => 'varchar-200 原图片地址',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'zdate' => 'int-11 抓取时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
    
    //建立一个mysql表 happy 抓取日志
    'happy_log' => array
    (
        'type'      => 'mysql',
        'host'      => $mysql['host'],
        'port'      => $mysql['port'],
        'username'  => $mysql['user'],
        'password'  => $mysql['pass'],
        'dbname'    => $mysql['dbname'],
        'charset'   => $mysql['charset'],
        'create'   => $mysql['create'],
        'col' => array
        (
            'id' => 'int-11',
            'config_id' => 'int-11 配置id',
            'request' => 'text-255 请求', 
            'data' => 'text-255 得到的数据', 
            'cdate' => 'int-11 创建时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
);
