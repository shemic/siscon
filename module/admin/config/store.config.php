<?php
/*
type的值：ttable、mysql、sqlite、redis、tt等
*/

$mysql = siscon::$global['define']['mysql'];

$store = array
(
    //建立一个mysql表 管理员表
    'admin' => array
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
            'name' => 'varchar-24 管理员账号',
            'password' => 'varchar-32 密码',
            'group_id' => 'int-11 部门id',
            'uid' => 'int-11 用户id',
            'role_id' => 'int-11 权限组id',
            'mobile' => 'varchar-24 手机号',
            'email' => 'varchar-100 管理员邮箱',
            'pic' => 'varchar-120 管理员头像',
            'info' => 'varchar-240 管理员介绍',
            'weibo' => 'varchar-120 管理员weibo',
            'truename' => 'varchar-120 真实姓名',
            'status' => 'int-1 冻结状态1正常2冻结不能登陆',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
	# 角色权限表
    'role' => array
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
            'name' => 'varchar-24 权限组名称',
            'auth' => 'text-255 权限',
            'menu' => 'text-255 菜单',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),

    # 部门表
    'group' => array
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
            'name' => 'varchar-24 部门名称',
            'connect' => 'text-255 部门联系方式',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
    
    # 权限表
    'auth' => array
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
            'name' => 'varchar-24 权限名',
            'auth' => 'varchar-100 权限',
            'cuser' => 'varchar-24 创建人',
            'muser' => 'varchar-24 修改人',
            'cdate' => 'int-11 创建时间',
            'mdate' => 'int-11 修改时间',
            'state' => 'int-1 状态1为存在2为删除',
        ),
    ),
);
