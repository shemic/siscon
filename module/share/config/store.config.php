<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 分享表
    'share_log' => array
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
            'uid' => 'int-11 用户id',
            'surl' => 'varchar-200 链接',
            'pic' => 'varchar-200 图片',
            'url' => 'varchar-200 链接',
            'title' => 'varchar-32 标题',
            'article' => 'int-11 文章id',
            'cate' => 'int-11 类别id',
            'site' => 'int-1 站点id',
            'platform' => 'varchar-100 平台',
            'browser' => 'varchar-50 浏览器',
            'city' => 'varchar-100 城市',
            'ip' => 'varchar-32 ip',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'status' => 'int-1 1为开放2为关闭',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
    
    //建立一个mysql表 分享信息表
    'share_total' => array
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
            'url_hash' => 'varchar-200 urlhash',
            'url' => 'varchar-200 链接',
            'total' => 'int-32 总数',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
    //建立一个mysql表 分享表
    'share_reflux' => array
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
            'uid' => 'int-11 用户id',
            'surl' => 'varchar-200 链接',
            'pic' => 'varchar-200 图片',
            'url' => 'varchar-200 链接',
            'title' => 'varchar-32 标题',
            'article' => 'int-11 文章id',
            'cate' => 'int-11 类别id',
            'site' => 'int-1 站点id',
            'platform' => 'varchar-100 平台',
            'browser' => 'varchar-50 浏览器',
            'city' => 'varchar-100 城市',
            'ip' => 'varchar-32 ip',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'status' => 'int-1 1为开放2为关闭',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
);
