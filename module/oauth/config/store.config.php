<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 分享表
    'oauth' => array
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
            'system' => 'varchar-30 所属项目',
            'name' => 'varchar-30 名称',
            'oid' => 'varchar-200 来源的oid',
            'sid' => 'int-11 来源的系统id',
            'uid' => 'int-11 对应的本站uid',
            'token_code' => 'varchar-200 生成的正式token信息',
            'token_refresh' => 'varchar-200 生成的正式token信息',
            'token_type' => 'varchar-200 生成的正式token信息',
            'token_time' => 'varchar-200 生成的正式token信息',
            'token_id' => 'int-11 生成的正式token信息',
            'mdate' => 'int-11 数据修改时间',
            'cdate' => 'int-11 数据添加时间',
            'state' => 'int-1 1是数据存在，2是数据删除',
            'ip'    => 'varchar-32 插入数据的ip',
        ),
    ),
);
