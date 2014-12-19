<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 用户表
    'user' => array
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
            'username' => 'varchar-24 用户名',
            'password' => 'varchar-32 用户密码',
            'sex' => 'varchar-10 性别',
            'email' => 'varchar-100 邮箱',
            'mobile' => 'varchar-32 手机号',
            'pic' => 'varchar-100 头像',
            'ding' => 'int-1 是否订阅电子报，1订阅',
            'level' => 'int-11 用户等级',
            'truename' => 'varchar-24 真实姓名',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'status' => 'int-1 1为开放2为关闭',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
    'user_ding' => array
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
            'email' => 'varchar-100 邮箱',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'status' => 'int-1 1为未发送邮件刚刚订阅2为已订阅3为退订',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
);
