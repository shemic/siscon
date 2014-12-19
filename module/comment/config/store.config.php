<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 点评表
    'comment' => array
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
            'type' => 'int-11 点评类型1为文章点评',
            'did' => 'int-11 点评来源id',
            'name' => 'varchar-24 点评标题',
            'content' => 'text-255 点评内容',
            'uid' => 'int-11 用户id',
            'weibo' => 'int-1 是否发布到新浪微博',
            'ip' => 'varchar-32 ip',
            'city' => 'varchar-100 城市',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
);
